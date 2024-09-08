<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Form\CopyGroupForm;
use VideoGamesRecords\CoreBundle\Form\Type\ChartTypeType;
use VideoGamesRecords\CoreBundle\Form\VideoProofOnly;

class GroupAdminController extends CRUDController
{
    /**
     * @param $id
     * @param Request $request
     * @return Response
     */
    public function copyAction($id, Request $request): Response
    {
        /** @var Group $group */
        $group = $this->admin->getSubject();

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $form = $this->createForm(CopyGroupForm::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->copy($group, $data['withLibs']);

            $this->addFlash('sonata_flash_success', 'Group was successfully copied.');
            return new RedirectResponse($this->admin->generateUrl('show', ['id' => $group->getId()]));
        }

        return $this->render(
            '@VideoGamesRecordsCore/Admin/Form/form.default.html.twig',
            [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'admin' => $this->admin,
                'object' => $group,
                'form' => $form,
                'title' => 'Copy => ' . $group->getGame()->getName() . ' / ' . $group->getName(),
                'action' => 'edit'
            ]
        );
    }

    /**
     * @param         $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addLibChartAction($id, Request $request): RedirectResponse|Response
    {
        /** @var Group $group */
        $group = $this->admin->getSubject();

        if ($group->getGame()->getGameStatus()->isActive()) {
            $this->addFlash('sonata_flash_error', 'Game is already activated');
            return new RedirectResponse(
                $this->admin->generateUrl(
                    'list',
                    ['filter' => $this->admin->getFilterParameters()]
                )
            );
        }

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $form = $this->createForm(ChartTypeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $type = $data['type'];
            $chartType = $em->getRepository('VideoGamesRecords\CoreBundle\Entity\ChartType')->find($type);

            foreach ($group->getCharts() as $chart) {
                $libChart = new ChartLib();
                $libChart->setType($chartType);
                $chart->addLib($libChart);
                $em->persist($libChart);
            }
            $em->flush();

            $this->addFlash('sonata_flash_success', 'Add all libchart on group successfully');
            return new RedirectResponse($this->admin->generateUrl('show', ['id' => $group->getId()]));
        }

        return $this->render(
            '@VideoGamesRecordsCore/Admin/Object/Group/form.add_libchart.html.twig',
            [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'admin' => $this->admin,
                'object' => $group,
                'form' => $form,
                'group' => $group,
                'action' => 'edit'
            ]
        );
    }

    /**
     * @param         $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function setVideoProofOnlyAction($id, Request $request): RedirectResponse|Response
    {
        /** @var Group $group */
        $group = $this->admin->getSubject();

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $form = $this->createForm(VideoProofOnly::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $isVideoProofOnly = $data['isVideoProofOnly'];
            foreach ($group->getCharts() as $chart) {
                $chart->setIsProofVideoOnly($isVideoProofOnly);
            }
            $em->flush();

            $this->addFlash('sonata_flash_success', 'All charts are updated successfully');
            return new RedirectResponse($this->admin->generateUrl('show', ['id' => $group->getId()]));
        }

        return $this->render(
            '@VideoGamesRecordsCore/Admin/Form/form.set_video_proof_only.html.twig',
            [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'admin' => $this->admin,
                'object' => $group,
                'form' => $form,
                'title' => $group->getGame()->getName() . ' / ' . $group->getName(),
                'action' => 'edit'
            ]
        );
    }
}
