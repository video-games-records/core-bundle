<?php
namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use VideoGamesRecords\CoreBundle\Handler\Video\YoutubeDataHandler;

/**
 * Class VideoAdminController
 */
class VideoAdminController extends CRUDController
{

    private YoutubeDataHandler $youtubeDataHandler;

    public function __construct(YoutubeDataHandler $youtubeDataHandler)
    {
        $this->youtubeDataHandler = $youtubeDataHandler;
    }


    /**
     * @param $id
     * @return RedirectResponse
     */
    public function majAction($id): RedirectResponse
    {
        $this->youtubeDataHandler->process($this->admin->getSubject());
        $this->addFlash('sonata_flash_success', 'Video data maj successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
