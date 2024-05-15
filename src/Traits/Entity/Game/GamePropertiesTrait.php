<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Game;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Entity\Game;
use Symfony\Component\Validator\Constraints as Assert;

trait GamePropertiesTrait
{
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id', nullable:false)]
    private Game $game;
}
