<?php
namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use VideoGamesRecords\CoreBundle\Ranking\Command\Player\PlayerRankingHandler;

/**
 * Class PlayerAdminController
 */
class PlayerAdminController extends CRUDController
{
    private PlayerRankingHandler $playerRankingHandler;

    public function __construct(PlayerRankingHandler $playerRankingHandler)
    {
        $this->playerRankingHandler = $playerRankingHandler;
    }

    /**
     * @param $id
     * @return RedirectResponse
     */
    public function majAction($id): RedirectResponse
    {
        $this->playerRankingHandler->handle($this->admin->getSubject());
        $this->addFlash('sonata_flash_success', 'Player maj successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
