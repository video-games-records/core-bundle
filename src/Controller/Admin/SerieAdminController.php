<?php
namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerSerieRankingHandler;

/**
 * Class GameAdminController
 */
class SerieAdminController extends CRUDController
{

    private PlayerSerieRankingHandler $rankingHandler;

    public function __construct(PlayerSerieRankingHandler $rankingHandler)
    {
        $this->rankingHandler = $rankingHandler;
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function majAction($id): RedirectResponse
    {
        $this->rankingHandler->handle($this->admin->getSubject()->getId());
        $this->addFlash('sonata_flash_success', 'Serie maj successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
