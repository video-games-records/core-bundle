<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use VideoGamesRecords\CoreBundle\Entity\Discord;

/**
 * @extends ServiceEntityRepository<Discord>
 *
 * @method Discord|null find($id, $lockMode = null, $lockVersion = null)
 * @method Discord|null findOneBy(array $criteria, array $orderBy = null)
 * @method Discord[]    findAll()
 * @method Discord[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class DiscordRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Discord::class);
    }
}