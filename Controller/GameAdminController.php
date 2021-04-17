<?php
namespace VideoGamesRecords\CoreBundle\Controller;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class GameAdminController
 */
class GameAdminController extends CRUDController
{
    /**
     * @param $id
     * @return RedirectResponse
     */
    public function copyAction($id)
    {
        if ($this->admin->hasAccess('create')) {
            $object = $this->admin->getSubject();

            if (!$object) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
            }

            $em = $this->admin->getModelManager()
                ->getEntityManager($this->admin->getClass());
            $em->getRepository('VideoGamesRecordsCoreBundle:Game')
                ->copy($id);

            $this->addFlash('sonata_flash_success', 'Copied successfully');
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
