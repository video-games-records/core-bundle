<?php

// src/Controller/PlayerChart/BulkUpsert.php
declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\PlayerChart;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Entity\PlayerChart;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartLib;
use VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus;
use VideoGamesRecords\CoreBundle\Entity\Platform;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerChartRank;
use Zenstruck\Messenger\Monitor\Stamp\DescriptionStamp;

class BulkUpsert extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private SerializerInterface $serializer;
    private ValidatorInterface $validator;
    private MessageBusInterface $messageBus;
    private TranslatorInterface $translator;

    public function __construct(
        EntityManagerInterface $entityManager,
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        MessageBusInterface $messageBus,
        TranslatorInterface $translator
    ) {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->messageBus = $messageBus;
        $this->translator = $translator;
    }

    public function __invoke(Request $request): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_PLAYER');

        $content = json_decode($request->getContent(), true);

        if (!isset($content['playerCharts']) || !is_array($content['playerCharts'])) {
            return new JsonResponse(['error' => 'playerCharts array is required'], Response::HTTP_BAD_REQUEST);
        }

        $playerCharts = [];
        $errors = [];
        $chartIds = [];
        $createdCount = 0;
        $updatedCount = 0;

        // Désactiver l'auto-flush pour les performances
        $this->entityManager->getConnection()->beginTransaction();

        try {
            foreach ($content['playerCharts'] as $index => $playerChartData) {
                $result = $this->processPlayerChartData($playerChartData, $index, $errors);

                if ($result !== null) {
                    $this->entityManager->persist($result['playerChart']);
                    $playerCharts[] = $result['playerChart'];
                    $chartIds[] = $result['playerChart']->getChart()->getId();

                    if ($result['isUpdate']) {
                        $updatedCount++;
                    } else {
                        $createdCount++;
                    }
                }
            }

            if (!empty($errors)) {
                $this->entityManager->getConnection()->rollBack();
                return new JsonResponse(['errors' => $errors], Response::HTTP_BAD_REQUEST);
            }

            // Flush toutes les entités en une fois
            $this->entityManager->flush();

            // Mettre à jour le game.lastScore avec le dernier PlayerChart du tableau
            if (!empty($playerCharts)) {
                $lastPlayerChart = end($playerCharts);
                $game = $lastPlayerChart->getChart()->getGroup()->getGame();
                $game->setLastScore($lastPlayerChart);
                $this->entityManager->flush();
            }

            $this->entityManager->getConnection()->commit();

            // Envoyer les messages de mise à jour des rangs de manière groupée
            $this->dispatchRankingMessages($chartIds);

            return new JsonResponse([
                'message' => $this->translator->trans(
                    'playerChart.bulk_upsert_success',
                    ['%total%' => count($playerCharts), '%created%' => $createdCount, '%updated%' => $updatedCount],
                    'VgrCore'
                ),
                'created' => $createdCount,
                'updated' => $updatedCount,
                'total' => count($playerCharts),
                'ranking_updates_dispatched' => count(array_unique($chartIds))
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            $this->entityManager->getConnection()->rollBack();
            return new JsonResponse(
                ['error' => 'Database error: ' . $e->getMessage()],
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    private function processPlayerChartData(array $data, int $index, array &$errors): ?array
    {
        try {
            // Extraction des IDs depuis les formats API Platform ou simples
            $chartId = $this->extractId($data['chart'] ?? null);
            $playerId = $this->extractId($data['player'] ?? null);

            // Validation des données requises
            if (!$chartId || !$playerId) {
                $errors[$index] = 'chart and player are required fields';
                return null;
            }

            // Récupération des entités référencées
            $chart = $this->entityManager->getRepository(Chart::class)->find($chartId);
            if (!$chart) {
                $errors[$index] = sprintf('Chart with id %s not found', $chartId);
                return null;
            }

            $player = $this->entityManager->getRepository(Player::class)->find($playerId);
            if (!$player) {
                $errors[$index] = sprintf('Player with id %s not found', $playerId);
                return null;
            }

            // Vérifier si c'est une mise à jour ou une création
            $isUpdate = false;
            $playerChart = null;
            $playerChartId = $data['id'] ?? null;

            if ($playerChartId) {
                // Mode modification : récupérer l'entité existante
                $playerChart = $this->entityManager->getRepository(PlayerChart::class)->find($playerChartId);
                if (!$playerChart) {
                    $errors[$index] = sprintf('PlayerChart with id %s not found', $playerChartId);
                    return null;
                }

                // Vérifier que l'utilisateur peut modifier ce PlayerChart
                if ($playerChart->getPlayer()->getId() !== $player->getId()) {
                    $errors[$index] = 'Cannot modify PlayerChart of another player';
                    return null;
                }

                $isUpdate = true;
            } else {
                // Mode création : vérifier qu'il n'existe pas déjà
                $existingPlayerChart = $this->entityManager->getRepository(PlayerChart::class)
                    ->findOneBy(['chart' => $chart, 'player' => $player]);

                if ($existingPlayerChart) {
                    $errors[$index] = sprintf(
                        'PlayerChart already exists for player %d and chart %d',
                        $player->getId(),
                        $chart->getId()
                    );
                    return null;
                }

                // Création du PlayerChart
                $playerChart = new PlayerChart();
                $playerChart->setChart($chart);
                $playerChart->setPlayer($player);
            }

            // Status - gérer les formats API Platform et simples
            $statusId = $this->extractId($data['status'] ?? null) ?? 1;
            $status = $this->entityManager->getRepository(PlayerChartStatus::class)->find($statusId);
            if (!$status) {
                $errors[$index] = 'Invalid status provided';
                return null;
            }
            $playerChart->setStatus($status);
            $playerChart->setProof();

            // Platform optionnelle - gérer les formats API Platform et simples
            if (isset($data['platform'])) {
                $platformId = $this->extractId($data['platform']);
                if ($platformId) {
                    $platform = $this->entityManager->getRepository(Platform::class)->find($platformId);
                    if (!$platform) {
                        $errors[$index] = sprintf('Platform with id %s not found', $platformId);
                        return null;
                    }
                    $playerChart->setPlatform($platform);
                }
            }

            // Gestion des libs (valeurs du score)
            if (isset($data['libs']) && is_array($data['libs'])) {
                if ($isUpdate) {
                    // En mode édition, mettre à jour les libs existantes
                    $this->updatePlayerChartLibs($playerChart, $data['libs'], $index, $errors);
                } else {
                    // En mode création, ajouter les nouvelles libs
                    foreach ($data['libs'] as $libData) {
                        $chartLibId = $this->extractId($libData['libChart'] ?? $libData['chartLib'] ?? null);
                        $parseValue = $libData['parseValue'] ?? null;
                        $value = $libData['value'] ?? null;

                        if (!$chartLibId || ($parseValue === null && $value === null)) {
                            $errors[$index] = 'libChart/chartLib and parseValue (or value) are required for each lib';
                            return null;
                        }

                        $chartLib = $this->entityManager
                            ->getRepository(ChartLib::class)
                            ->find($chartLibId);

                        if (!$chartLib) {
                            $errors[$index] = sprintf('ChartLib with id %s not found', $chartLibId);
                            return null;
                        }

                        $playerChartLib = new PlayerChartLib();
                        $playerChartLib->setLibChart($chartLib);

                        if ($parseValue !== null) {
                            // Si parseValue est fourni, l'utiliser et appeler setValueFromPaseValue()
                            $playerChartLib->setParseValue($parseValue);
                            $playerChartLib->setValueFromPaseValue();
                        } else {
                            // Sinon utiliser value directement (rétrocompatibilité)
                            $playerChartLib->setValue($value);
                        }

                        $playerChart->addLib($playerChartLib);
                    }
                }

                if (!empty($errors)) {
                    return null;
                }
            }

            // Mettre à jour lastUpdate avec la date du jour et forcer le statut à 1
            $playerChart->setLastUpdate(new \DateTime());
            $defaultStatus = $this->entityManager->getRepository(PlayerChartStatus::class)->find(1);
            if ($defaultStatus) {
                $playerChart->setStatus($defaultStatus);
            }

            // Validation de l'entité
            $violations = $this->validator->validate($playerChart);
            if (count($violations) > 0) {
                $violationMessages = [];
                foreach ($violations as $violation) {
                    $violationMessages[] = $violation->getMessage();
                }
                $errors[$index] = implode(', ', $violationMessages);
                return null;
            }

            return [
                'playerChart' => $playerChart,
                'isUpdate' => $isUpdate
            ];
        } catch (\Exception $e) {
            $errors[$index] = 'Error processing PlayerChart: ' . $e->getMessage();
            return null;
        }
    }

    private function extractId($value): ?int
    {
        if (is_numeric($value)) {
            return (int) $value;
        }

        if (is_string($value) && preg_match('/\/(\d+)$/', $value, $matches)) {
            return (int) $matches[1];
        }

        if (is_array($value) && isset($value['id'])) {
            return (int) $value['id'];
        }

        return null;
    }

    private function updatePlayerChartLibs(PlayerChart $playerChart, array $libsData, int $index, array &$errors): void
    {
        // Créer un index des libs existantes par chartLibId
        $existingLibs = [];
        foreach ($playerChart->getLibs() as $existingLib) {
            $existingLibs[$existingLib->getLibChart()->getId()] = $existingLib;
        }

        // Traiter chaque lib des données
        foreach ($libsData as $libData) {
            $chartLibId = $this->extractId($libData['libChart'] ?? $libData['chartLib'] ?? null);
            $parseValue = $libData['parseValue'] ?? null;
            $value = $libData['value'] ?? null;

            if (!$chartLibId || ($parseValue === null && $value === null)) {
                $errors[$index] = 'libChart/chartLib and parseValue (or value) are required for each lib';
                return;
            }

            if (isset($existingLibs[$chartLibId])) {
                // Mettre à jour la lib existante
                if ($parseValue !== null) {
                    $existingLibs[$chartLibId]->setParseValue($parseValue);
                    $existingLibs[$chartLibId]->setValueFromPaseValue();
                } else {
                    $existingLibs[$chartLibId]->setValue($value);
                }
            } else {
                // Créer une nouvelle lib si elle n'existe pas
                $chartLib = $this->entityManager->getRepository(ChartLib::class)
                    ->find($chartLibId);

                if (!$chartLib) {
                    $errors[$index] = sprintf('ChartLib with id %s not found', $chartLibId);
                    return;
                }

                $playerChartLib = new PlayerChartLib();
                $playerChartLib->setLibChart($chartLib);

                if ($parseValue !== null) {
                    $playerChartLib->setParseValue($parseValue);
                    $playerChartLib->setValueFromPaseValue();
                } else {
                    $playerChartLib->setValue($value);
                }

                $playerChart->addLib($playerChartLib);
            }
        }
    }

    private function dispatchRankingMessages(array $chartIds): void
    {
        // Envoyer un message par chart unique pour éviter la duplication
        $uniqueChartIds = array_unique($chartIds);

        foreach ($uniqueChartIds as $chartId) {
            $message = new UpdatePlayerChartRank($chartId);
            $this->messageBus->dispatch(
                $message,
                [
                    new DescriptionStamp(
                        sprintf('Bulk update player-ranking for chart [%d]', $chartId)
                    )
                ]
            );
        }
    }
}
