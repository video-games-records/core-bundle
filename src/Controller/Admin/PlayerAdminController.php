<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use VideoGamesRecords\CoreBundle\Message\Player\UpdatePlayerData;

class PlayerAdminController extends CRUDController
{
    public function __construct(private MessageBusInterface $bus)
    {
    }

    /**
     * @param $id
     * @return RedirectResponse
     * @throws ExceptionInterface
     */
    public function majAction($id): RedirectResponse
    {
        $this->bus->dispatch(new UpdatePlayerData($this->admin->getSubject()->getId()));
        $this->addFlash('sonata_flash_success', 'Player maj successfully');
        return new RedirectResponse($this->admin->generateUrl('list'));
    }
}
