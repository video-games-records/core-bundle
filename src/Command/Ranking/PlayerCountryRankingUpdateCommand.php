<?php
namespace VideoGamesRecords\CoreBundle\Command\Ranking;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use VideoGamesRecords\CoreBundle\Contracts\Ranking\RankingCommandInterface;
use VideoGamesRecords\CoreBundle\Entity\Country;

class PlayerCountryRankingUpdateCommand extends Command
{
    protected static $defaultName = 'vgr-core:country-ranking-update';

    private EntityManagerInterface $em;
    private RankingCommandInterface $rankingCommand;

    public function __construct(EntityManagerInterface $em, RankingCommandInterface $rankingCommand)
    {
        $this->em = $em;
        $this->rankingCommand = $rankingCommand;
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('vgr-core:country-ranking-update')
            ->setDescription('Command to update players country ranking')
        ;
        parent::configure();
    }


    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $countries = $this->em->getRepository(Country::class)->findBy(['boolMaj' => true]);
        /** @var Country $country */
        foreach ($countries as $country) {
            $this->rankingCommand->handle($country->getId());
            $country->setBoolMaj(false);
        }
        $this->em->flush();

        return Command::SUCCESS;
    }
}
