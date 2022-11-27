<?php
namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
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
    public function copyAction($id): RedirectResponse
    {
        if ($this->admin->hasAccess('create')) {
            $game = $this->admin->getSubject();

            if (!$game) {
                throw new NotFoundHttpException(sprintf('unable to find the object with id: %s', $id));
            }

            $this->getRepository()->copy($id);
            $this->addFlash('sonata_flash_success', 'Copied successfully');
        }

        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function majAction($id): RedirectResponse
    {
        $game = $this->admin->getSubject();
        $this->getRepository()->majChartStatus($game);
        return new RedirectResponse($this->admin->generateUrl('list'));
    }

    /**
     * @return EntityRepository|ObjectRepository
     */
    private function getRepository()
    {
        $em = $this->admin->getModelManager()->getEntityManager($this->admin->getClass());
        return $em->getRepository('VideoGamesRecords\CoreBundle\Entity\Game');
    }
}
