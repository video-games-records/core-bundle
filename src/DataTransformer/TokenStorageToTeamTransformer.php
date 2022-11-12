<?php

namespace VideoGamesRecords\CoreBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Form\DataTransformerInterface;
use VideoGamesRecords\CoreBundle\Entity\Team;

class TokenStorageToTeamTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $value
     * @return ?Team
     * @throws ORMException
     */
    public function transform($value): ?Team
    {
        if ($value) {
            $player = $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $value->getUser()->getId());
            return $player->getTeam();
        }
        return null;
    }


    public function reverseTransform($value): array
    {
        //@todo
        return [];
    }
}
