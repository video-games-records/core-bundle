<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Doctrine\ORM\TransactionRequiredException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use VideoGamesRecords\CoreBundle\Repository\PlayerSerieRepository;
use VideoGamesRecords\CoreBundle\Repository\SerieRepository;

class SerieService
{
    private SerieRepository $serieRepository;
    private PlayerSerieRepository $playerSerieRepository;

    public function __construct(SerieRepository $serieRepository, PlayerSerieRepository $playerSerieRepository)
    {
        $this->serieRepository = $serieRepository;
        $this->playerSerieRepository = $playerSerieRepository;
    }

    /**
     * @param $idSerie
     * @throws ORMException
     * @throws OptimisticLockException
     * @throws TransactionRequiredException
     * @throws ExceptionInterface
     */
    public function maj($idSerie)
    {
        $this->playerSerieRepository->maj($idSerie);
    }
}
