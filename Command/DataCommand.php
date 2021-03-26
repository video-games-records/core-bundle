<?php

namespace VideoGamesRecords\CoreBundle\Command;

use Doctrine\DBAL\DBALException;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\EntityManagerInterface;

class DataCommand extends DefaultCommand
{
    protected static $defaultName = 'vgr-core:data';

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
        parent::__construct($em);
    }

    protected function configure()
    {
        $this
            ->setName('vgr-core:data')
            ->setDescription('Command to update data')
            ->addArgument(
                'function',
                InputArgument::REQUIRED,
                'What do you want to do?'
            )
            ->addOption(
                'debug',
                null,
                InputOption::VALUE_NONE,
                ''
            );
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->init($input);
        $function = $input->getArgument('function');
        switch ($function) {
            case 'maj-user-record-connexion':
                $this->majUserRecordConnexion($output);
                break;
        }
        $this->end($output);

        return 0;
    }

    /**
     * @param OutputInterface $output
     * @throws Exception
     */
    private function majUserRecordConnexion(OutputInterface $output)
    {
        /** @var DataRepository $dataRepository */
        $dataRepository = $this->em->getRepository('VideoGamesRecordsCoreBundle:Data');
        /** @var UserRepository $dataRepository */
        $userRepository = $this->em->getRepository('ProjetNormandieUserBundle:User');

        $date = new \DateTime();

        $list = $userRepository->getLoggedToday($date);
        $nb = count($list);

        $oDataAllDate = $dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'DATE', 'version' => 'ALL'));
        $oDataAllNb = $dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'NB', 'version' => 'ALL'));
        $oDataV7Date = $dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'DATE', 'version' => 'V7'));
        $oDataV7Nb = $dataRepository->findOneBy(array('category' => 'USER_RECORD_CONNEXION', 'label' => 'NB', 'version' => 'V7'));

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
        $this->em->flush();
    }
}
