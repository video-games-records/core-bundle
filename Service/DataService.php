<?php

namespace VideoGamesRecords\CoreBundle\Service;

use Doctrine\ORM\ORMException;
use VideoGamesRecords\CoreBundle\Repository\DataRepository;
use ProjetNormandie\UserBundle\Service\UserService;

class DataService
{
    private DataRepository $dataRepository;
    private UserService $userService;

    public function __construct(
        DataRepository $dataRepository,
        UserService $userService
    )
    {
        $this->dataRepository = $dataRepository;
        $this->userService = $userService;
    }

    /**
     * @throws ORMException
     */
    public function majUserRecordConnexion()
    {
        $date = new \DateTime();

        $list = $this->userService->getLoggedToday($date);
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
