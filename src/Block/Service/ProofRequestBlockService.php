<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Block\Service;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use VideoGamesRecords\CoreBundle\ValueObject\ProofRequestStatus;

class ProofRequestBlockService extends AbstractBlockService
{
    public function __construct(Environment $templating, private readonly EntityManagerInterface $em)
    {
        parent::__construct($templating);
    }

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Proof Request Block';
    }

    /**
     * @param BlockContextInterface $blockContext
     * @param Response|null $response
     * @return Response
     */
    public function execute(
        BlockContextInterface $blockContext,
        ?Response $response = null
    ): Response {
        $settings = $blockContext->getSettings();

        // Requête pour récupérer les demandes de preuve en cours
        $proofRequestsQuery = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\ProofRequest', 'pr')
            ->select('pr')
            ->addSelect('pc', 'p', 'c', 'g', 'game')
            ->leftJoin('pr.playerChart', 'pc')
            ->leftJoin('pc.player', 'p')
            ->leftJoin('pc.chart', 'c')
            ->leftJoin('c.group', 'g')
            ->leftJoin('g.game', 'game')
            ->leftJoin('pr.playerRequesting', 'requesting')
            ->leftJoin('pr.playerResponding', 'responding')
            ->where('pr.status = :status')
            ->setParameter('status', ProofRequestStatus::IN_PROGRESS)
            ->orderBy('pr.createdAt', 'DESC');

        $proofRequests = $proofRequestsQuery->getQuery()->getResult();

        // Nombre total de demandes en cours
        $totalRequestsQuery = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\ProofRequest', 'pr')
            ->select('COUNT(pr.id)')
            ->where('pr.status = :status')
            ->setParameter('status', ProofRequestStatus::IN_PROGRESS);

        $totalRequests = (int)$totalRequestsQuery->getQuery()->getSingleScalarResult();


        return $this->renderResponse(
            '@VideoGamesRecordsCore/Admin/Block/proof_requests.html.twig',
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'proofRequests' => $proofRequests,
                'totalRequests' => $totalRequests
            ],
            $response
        );
    }
}
