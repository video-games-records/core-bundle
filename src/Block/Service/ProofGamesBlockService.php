<?php

namespace VideoGamesRecords\CoreBundle\Block\Service;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ProofGamesBlockService extends AbstractBlockService
{
    private EntityManagerInterface $entityManager;

    public function __construct(Environment $templating, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
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
        $games = $this->entityManager->getRepository('VideoGamesRecords\CoreBundle\Entity\Game')->getNbProofInProgress();

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
