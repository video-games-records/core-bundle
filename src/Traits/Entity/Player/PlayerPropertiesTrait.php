<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Player;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Entity\Player;
use Symfony\Component\Validator\Constraints as Assert;

trait PlayerPropertiesTrait
{
    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'player_id', referencedColumnName:'id', nullable:false)]
    private Player $player;
}
