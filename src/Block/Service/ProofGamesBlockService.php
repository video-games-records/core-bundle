<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Block\Service;

use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use VideoGamesRecords\CoreBundle\DataProvider\ProofInProgressProvider;

class ProofGamesBlockService extends AbstractBlockService
{
    private ProofInProgressProvider $proofInProgressProvider;

    public function __construct(Environment $templating, ProofInProgressProvider $proofInProgressProvider)
    {
        $this->proofInProgressProvider = $proofInProgressProvider;
        parent::__construct($templating);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Proof Games Block';
    }


    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null         $response
     * @return Response
     */
    public function execute(
        BlockContextInterface $blockContext,
        Response $response = null
    ): Response {
        $settings = $blockContext->getSettings();
        $games = $this->proofInProgressProvider->loadByGame();

        return $this->renderResponse(
            '@VideoGamesRecordsCore/Admin/Block/proof.games.html.twig',
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'games' => $games,
            ],
            $response
        );
    }
}
