<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Block\Service;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;
use VideoGamesRecords\CoreBundle\ValueObject\ProofStatus;

class ProofGamesBlockService extends AbstractBlockService
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
        return 'Proof Games Block';
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

        $query = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\Game', 'gam')
            ->select('gam')
            ->addSelect('COUNT(proof) as nb')
            ->innerJoin('gam.groups', 'grp')
            ->innerJoin('grp.charts', 'chr')
            ->innerJoin('chr.proofs', 'proof')
            ->where('proof.status = :status')
            ->setParameter('status', ProofStatus::IN_PROGRESS)
            ->groupBy('gam.id')
            ->orderBy('nb', 'DESC');

        $games = $query->getQuery()->getResult();

        $totalProofsQuery = $this->em->createQueryBuilder()
            ->from('VideoGamesRecords\CoreBundle\Entity\Proof', 'proof')
            ->select('COUNT(proof.id)')
            ->where('proof.status = :status')
            ->setParameter('status', ProofStatus::IN_PROGRESS);

        $totalProofs = (int) $totalProofsQuery->getQuery()->getSingleScalarResult();

        return $this->renderResponse(
            '@VideoGamesRecordsCore/Admin/Block/proofs_by_game.html.twig',
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
                'games' => $games,
                'totalProofs' => $totalProofs,
            ],
            $response
        );
    }
}
