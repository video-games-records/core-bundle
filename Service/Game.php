<?php

namespace VideoGamesRecords\CoreBundle\Service;

use VideoGamesRecords\CoreBundle\Entity\Group;
use VideoGamesRecords\CoreBundle\Entity\Chart;
use VideoGamesRecords\CoreBundle\Entity\ChartLib;
use VideoGamesRecords\CoreBundle\Entity\Game as GameEntity;
use VideoGamesRecords\CoreBundle\Entity\ChartType;

class Game
{
    private $em;
    private $directory;

    public function __construct(\Doctrine\ORM\EntityManager $em, $rootDir)
    {
        $this->em        = $em;
        $this->directory = $rootDir . '/../var/data/game';
    }

    /**
     * Add groups and charts from a csv file
     */
    public function addFromCsv()
    {
        $files = array_diff(scandir($this->directory . '/in', SCANDIR_SORT_NONE), ['..', '.']);
        foreach ($files as $file) {
            if (0 !== strpos($file, 'game-add')) {
                continue;
            }

            $fileIn  = $this->directory . '/in/' . $file;
            $fileOut = $this->directory . '/out/' . $file;
            $handle  = fopen($fileIn, 'rb');
            $idGame  = substr($file, 8, -4);

            if (!is_numeric($idGame)) {
                continue;
            }

            /** @var \VideoGamesRecords\CoreBundle\Entity\Game $game */
            $game = $this->em->getReference(GameEntity::class, $idGame);

            if ($game === null) {
                continue;
            }

            $group = null;
            $types = null;
            while (($row = fgetcsv($handle, null, ';')) !== false) {
                list($type, $libEn, $libFr) = $row;
                if (isset($row[3]) && null !== $row[3] && in_array($type, ['group', 'chart'])) {
                    $types = explode('|', $row[3]);
                }
                switch ($row[0]) {
                    case 'object':
                        // DO nohting
                        break;
                    case 'group':
                        $group = new Group();
                        $group->translate('en', false)->setName($libEn);
                        $group->translate('fr', false)->setName($libFr);
                        $group->setGame($game);
                        $group->mergeNewTranslations();
                        $this->em->persist($group);
                        break;
                    case 'chart':
                        $chart = new Chart();
                        $chart->translate('en', false)->setName($libEn);
                        $chart->translate('fr', false)->setName($libFr);
                        $chart->setGroup($group);
                        $chart->mergeNewTranslations();

                        if ($types !== null) {
                            foreach ($types as $idType) {
                                $chartLib = new ChartLib();
                                $chartLib
                                    ->setChart($chart)
                                    ->setType($this->em->getReference(ChartType::class, $idType));
                                $chart->addLib($chartLib);
                            }
                        }

                        $this->em->persist($chart);
                        break;
                }
            }
            $this->em->flush();
            rename($fileIn, $fileOut);
        }
    }

    /**
     * Update groups and charts from a csv file
     */
    public function updateFromCsv()
    {
        $files = array_diff(scandir($this->directory . '/in', SCANDIR_SORT_NONE), ['..', '.']);
        foreach ($files as $file) {
            if (0 !== strpos($file, 'game-update')) {
                continue;
            }

            $fileIn  = $this->directory . '/in/' . $file;
            $fileOut = $this->directory . '/out/' . $file;
            $handle  = fopen($fileIn, 'rb');
            $idGame  = substr($file, 11, -4);

            if (!is_numeric($idGame)) {
                continue;
            }

            $group = null;
            $types = null;
            while (($row = fgetcsv($handle, null, ';')) !== false) {
                list($type, $id, $libEn, $libFr) = $row;
                switch ($type) {
                    case 'object':
                        // DO nothing
                        break;
                    case 'group':
                        $group = $this->em->getRepository('VideoGamesRecordsCoreBundle:Group')->find($id);
                        $group->translate('en', false)->setName($libEn);
                        $group->translate('fr', false)->setName($libFr);
                        $group->mergeNewTranslations();
                        $this->em->persist($group);
                        break;
                    case 'chart':
                        $chart = $this->em->getRepository('VideoGamesRecordsCoreBundle:Chart')->find($id);
                        $chart->translate('en', false)->setName($libEn);
                        $chart->translate('fr', false)->setName($libFr);
                        $chart->mergeNewTranslations();
                        $this->em->persist($chart);
                        break;
                }
            }
            $this->em->flush();
            rename($fileIn, $fileOut);
        }
    }
}
