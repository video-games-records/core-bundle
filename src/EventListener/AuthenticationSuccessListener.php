<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\EventListener;

use Doctrine\ORM\Exception\ORMException;
use ProjetNormandie\UserBundle\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\AuthenticationSuccessEvent;
use VideoGamesRecords\CoreBundle\DataTransformer\UserToPlayerTransformer;

class AuthenticationSuccessListener
{
    public function __construct(
        private readonly UserToPlayerTransformer $userToPlayerTransformer
    ) {
    }

    /**
     * @throws ORMException
     */
    public function onAuthenticationSuccessResponse(AuthenticationSuccessEvent $event): void
    {
        $data = $event->getData();
        $user = $event->getUser();

        if (!$user instanceof User) {
            return;
        }

        $player = $this->userToPlayerTransformer->transform($user);
        $data['player'] = [
            'id' => $player->getId(),
            'slug' => $player->getSlug(),
            'team' => null,
        ];

        $team = $player->getTeam();
        if ($team !== null) {
            $data['player']['team'] = [
                'id' => $team->getId(),
                'slug' => $team->getSlug(),
            ];
        }

        $event->setData($data);
    }
}
