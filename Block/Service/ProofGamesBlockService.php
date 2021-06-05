<?php

namespace VideoGamesRecords\CoreBundle\Block\Service;

use Doctrine\ORM\EntityManagerInterface;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\BlockBundle\Block\Service\AbstractBlockService;
use Sonata\BlockBundle\Block\BlockContextInterface;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;


class ProofGamesBlockService extends AbstractBlockService
{
    private $entityManager;

    public function __construct(Environment $templating, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        parent::__construct($templating);
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
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

        return $this->renderResponse(
            'VideoGamesRecordsCoreBundle:Admin/Block:proof.games.html.twig',
            [
                'block' => $blockContext->getBlock(),
                'settings' => $settings,
            ],
            $response
        );
    }
}
