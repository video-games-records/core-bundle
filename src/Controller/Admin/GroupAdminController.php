<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Form\Type\ChartTypeType;

class GroupAdminController extends CRUDController
{
    /**
     * @param $id
     * @return RedirectResponse
     */
    public function copyAction($id): RedirectResponse
    {
        $group = $this->admin->getSubject();

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->copy($group, false);

        $this->addFlash('sonata_flash_success', 'Copied successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function copyWithLibChartAction($id): RedirectResponse
    {
        $group = $this->admin->getSubject();

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->copy($group, true);

        $this->addFlash('sonata_flash_success', 'Copied with libchart successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param         $id
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function addLibChartAction($id, Request $request): RedirectResponse|Response
    {
        /** @var Group $object */
        $group = $this->admin->getSubject();

        if ($group->getGame()->getGameStatus()->isActive()) {
            $this->addFlash('sonata_flash_error', 'Game is already activated');
            return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()]));
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
            '@VideoGamesRecordsCore/Admin/Group/form.add_chart.html.twig',
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
}
