<?php
namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerSerieRankingHandler;
use VideoGamesRecords\CoreBundle\Ranking\Command\Team\TeamSerieRankingHandler;

/**
 * Class GameAdminController
 */
class SerieAdminController extends CRUDController
{

    private PlayerSerieRankingHandler $playerSerieRankingHandler;
    private TeamSerieRankingHandler $teamSerieRankingHandler;

    public function __construct(
        PlayerSerieRankingHandler $playerSerieRankingHandler,
        TeamSerieRankingHandler $teamSerieRankingHandler)
    {
        $this->playerSerieRankingHandler = $playerSerieRankingHandler;
        $this->teamSerieRankingHandler = $teamSerieRankingHandler;
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function majAction($id): RedirectResponse
    {
        $this->playerSerieRankingHandler->handle($this->admin->getSubject()->getId());
        $this->teamSerieRankingHandler->handle($this->admin->getSubject()->getId());
        $this->addFlash('sonata_flash_success', 'Serie maj successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
