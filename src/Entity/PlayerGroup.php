<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

/**
 * PlayerGroup
 *
 * @ORM\Table(name="vgr_player_group")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository")
 */
class PlayerGroup
{
    use NbChartTrait;
    use NbChartProvenTrait;
    use RankMedalTrait;
    use RankPointChartTrait;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Player $player;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGroup", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Group $group;


    /**
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=true)
     */
    private DateTime $lastUpdate;


    /**
     * Set lastUpdate
     * @param DateTime $lastUpdate
     * @return $this
     */
    public function setLastUpdate(DateTime $lastUpdate): static
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return DateTime
     */
    public function getLastUpdate(): DateTime
    {
        return $this->lastUpdate;
    }


    /**
     * Set group
     * @param Group $group
     * @return $this
     */
    public function setGroup(Group $group): static
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group
     *
     * @return Group
     */
    public function getGroup(): Group
    {
        return $this->group;
    }


    /**
     * Set player
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player): static
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}
