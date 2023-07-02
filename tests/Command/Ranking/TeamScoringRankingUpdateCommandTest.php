<?php

namespace VideoGamesRecords\CoreBundle\Tests\Command\Ranking;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use VideoGamesRecords\CoreBundle\Command\Ranking\TeamScoringRankingUpdateCommand;

class TeamScoringRankingUpdateCommandTest extends KernelTestCase
{
    public function testServiceDeclaration()
    {
        self::bootKernel();

        $command = self::getContainer()
            ->get(TeamScoringRankingUpdateCommand::class);

        $this->assertInstanceOf(TeamScoringRankingUpdateCommand::class, $command);
    }
}
