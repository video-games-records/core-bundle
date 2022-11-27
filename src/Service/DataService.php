<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Repository\DataRepository;

class DataService
{
    private DataRepository $dataRepository;

    public function __construct(DataRepository $dataRepository)
    {
        $this->dataRepository = $dataRepository;
    }

    /**
     * @throws ORMException
     */
    public function majUserRecordConnexion(): void
    {
        $date = new \DateTime();

        //$list = $this->userService->getLoggedToday($date);
        $list = [];
        $nb = count($list);

        $oDataAllDate = $this->dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'DATE', 'version' => 'ALL'));
        $oDataAllNb = $this->dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'NB', 'version' => 'ALL'));
        $oDataV7Date = $this->dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'DATE', 'version' => 'V7'));
        $oDataV7Nb = $this->dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'NB', 'version' => 'V7'));

        // MAJ V7
        if ($nb > $oDataV7Nb->getValue()) {
            $oDataV7Date->setValue($date->format('d/m/Y'));
            $oDataV7Nb->setValue($nb);
        }
        // MAJ ALL
        if ($nb > $oDataAllNb->getValue()) {
            $oDataAllDate->setValue($date->format('d/m/Y'));
            $oDataAllNb->setValue($nb);
        }
        $this->dataRepository->flush();
    }
}
