<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\Contracts\SecurityInterface;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Form\DefaultForm;
use VideoGamesRecords\CoreBundle\Form\VideoProofOnly;
use VideoGamesRecords\CoreBundle\Manager\GameManager;
use Yokai\SonataWorkflow\Controller\WorkflowControllerTrait;

class GameAdminController extends CRUDController implements SecurityInterface
{
    use WorkflowControllerTrait;

    private GameManager $gameManager;
    private Security $security;

    public function __construct(GameManager $gameManager, Security $security)
    {
        $this->gameManager = $gameManager;
        $this->security = $security;
    }

    /**
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function copyAction($id, Request $request): Response
    {
        /** @var Game $game */
        $game = $this->admin->getSubject();

        if (!$this->isGranted(self::ROLE_SUPER_ADMIN)) {
            $this->addFlash('sonata_flash_error', 'The game was not copied.');
            return new RedirectResponse($this->admin->generateUrl('show', ['id' => $game->getId()]));
        }

        $form = $this->createForm(DefaultForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->gameManager->copy($game);
            $this->addFlash('sonata_flash_success', 'The game was successfully copied.');
            return new RedirectResponse($this->admin->generateUrl('show', ['id' => $game->getId()]));
        }

        return $this->render(
            '@VideoGamesRecordsCore/Admin/Form/form.default.html.twig',
            [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'admin' => $this->admin,
                'object' => $game,
                'form' => $form,
                'title' => 'Copy => ' . $game->getName(),
                'action' => 'edit'
            ]
        );
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function majAction($id): RedirectResponse
    {
        $this->gameManager->maj($this->admin->getSubject());
        $this->addFlash('sonata_flash_success', 'Game maj successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param         $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function setVideoProofOnlyAction($id, Request $request): RedirectResponse|Response
    {
        /** @var Game $game */
        $game = $this->admin->getSubject();

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $form = $this->createForm(VideoProofOnly::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $isVideoProofOnly = $data['isVideoProofOnly'];
            foreach ($game->getGroups() as $group) {
                foreach ($group->getCharts() as $chart) {
                    $chart->setIsProofVideoOnly($isVideoProofOnly);
                }
            }
            $em->flush();

            $this->addFlash('sonata_flash_success', 'All charts are updated successfully');
            return new RedirectResponse($this->admin->generateUrl('show', ['id' => $game->getId()]));
        }

        return $this->render(
            '@VideoGamesRecordsCore/Admin/Form/form.set_video_proof_only.html.twig',
            [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'admin' => $this->admin,
                'object' => $game,
                'form' => $form,
                'title' => $game->getName(),
                'action' => 'edit'
            ]
        );
    }
}
