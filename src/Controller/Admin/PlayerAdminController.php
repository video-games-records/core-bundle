<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;
use VideoGamesRecords\CoreBundle\Message\Dispatcher\RankingUpdateDispatcher;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerData;

class PlayerAdminController extends CRUDController
{
    public function __construct(private readonly RankingUpdateDispatcher $rankingUpdateDispatcher)
    {
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @throws ExceptionInterface
     */
    public function majAction($id): RedirectResponse
    {
        /** @var Player $player */
        $player = $this->admin->getSubject();
        $this->rankingUpdateDispatcher->updatePlayerRankFromPlayer($player);
        $this->addFlash('sonata_flash_success', 'Player maj successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
