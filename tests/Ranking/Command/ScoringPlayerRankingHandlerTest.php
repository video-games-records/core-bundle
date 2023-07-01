<?php

namespace VideoGamesRecords\CoreBundle\Tests\Ranking\Command;

use VideoGamesRecords\CoreBundle\Tests\AbstractFunctionalTestCase;

class ScoringPlayerRankingHandlerTest extends AbstractFunctionalTestCase
{
    public function testDefault(): void
    {
        $this->assertSame(1, 1);
    }
}
