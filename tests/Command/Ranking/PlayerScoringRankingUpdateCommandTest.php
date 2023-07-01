<?php

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use VideoGamesRecords\CoreBundle\Command\Ranking\PlayerScoringRankingUpdateCommand;

class PlayerScoringRankingUpdateCommandTest extends KernelTestCase
{
    public function testServiceDeclaration()
    {
        self::bootKernel();

        $command = self::getContainer()->get(PlayerScoringRankingUpdateCommand::class);

        $this->assertInstanceOf(PlayerScoringRankingUpdateCommand::class, $command);
    }
}
