<?php
namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use VideoGamesRecords\CoreBundle\Entity\Game;
use VideoGamesRecords\CoreBundle\Form\Type\ChartTypeType;

/**
 * Class GroupAdminController
 */
class GroupAdminController extends CRUDController
{
    /**
     * @param $id
     * @return RedirectResponse
     */
    public function copyAction($id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->copy($id, false);

        $this->addFlash('sonata_flash_success', 'Copied successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function copyWithLibChartAction($id)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->copy($id, true);

        $this->addFlash('sonata_flash_success', 'Copied with libchart successfully');

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param         $id
     * @param Request $request
     * @return Response
     */
    public function addLibChartAction($id, Request $request)
    {
        $object = $this->admin->getSubject();

        if (!$object) {
            throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
        }

        if ($object->getGame()->getStatus() == Game::STATUS_ACTIVE) {
            $this->addFlash('sonata_flash_error', 'Game is already activated');
            return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()]));
        }

        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        $form = $this->createForm(ChartTypeType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $type = $data['type'];
            $result = $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Group')->insertLibChart($id, $type->getIdType());
            if ($result) {
                $this->addFlash('sonata_flash_success', 'Add all libchart on group successfully');
                return new RedirectResponse($this->admin->generateUrl('list', ['filter' => $this->admin->getFilterParameters()]));
            } else {
                $this->addFlash('sonata_flash_error', 'Add all libchart on group successfully');
                return new RedirectResponse($this->admin->generateUrl('add-lib-chart'));
            }
        }

        return $this->renderForm('@VideoGamesRecordsCore/Admin/group_add_chart_form.html.twig',
            [
                'base_template' => '@SonataAdmin/standard_layout.html.twig',
                'admin' => $this->admin,
                'object' => $object,
                'form' => $form,
                'group' => $object,
                'action' => 'edit'
            ]
        );
    }
}
