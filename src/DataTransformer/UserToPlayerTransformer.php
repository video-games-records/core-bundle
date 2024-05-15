<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Form\DataTransformerInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;

class UserToPlayerTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $value
     * @return Player
     * @throws ORMException
     */
    public function transform($value): Player
    {
        return $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $value->getId());
    }


    public function reverseTransform($value): array
    {
        return [];
    }
}
