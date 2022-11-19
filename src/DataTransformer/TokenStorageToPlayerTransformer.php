<?php

namespace VideoGamesRecords\CoreBundle\DataTransformer;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\ORMException;
use Symfony\Component\Form\DataTransformerInterface;
use VideoGamesRecords\CoreBundle\Entity\Player;

class TokenStorageToPlayerTransformer implements DataTransformerInterface
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param $value
     * @return ?Player
     * @throws ORMException
     */
    public function transform($value): ?Player
    {
        if ($value) {
            return $this->em->getReference('VideoGamesRecords\CoreBundle\Entity\Player', $value->getUser()->getId());
        }
        return null;
    }


    public function reverseTransform($value): array
    {
        //@todo
        return [];
    }
}
