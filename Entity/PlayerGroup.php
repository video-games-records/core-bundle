<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerGroup
 *
 * @ORM\Table(name="vgr_player_group")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerGroupRepository")
 */
class PlayerGroup
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="playerGroup")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private Player $player;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGroup", referencedColumnName="id", nullable=false)
     * })
     */
    private Group $group;

    /**
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false)
     */
    private int $rankPointChart;

    /**
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private int $rankMedal;

    /**
     * @ORM\Column(name="chartRank0", type="integer", nullable=false)
     */
    private int $chartRank0;

    /**
     * @ORM\Column(name="chartRank1", type="integer", nullable=false)
     */
    private int $chartRank1;

    /**
     * @ORM\Column(name="chartRank2", type="integer", nullable=false)
     */
    private int $chartRank2;

    /**
     * @ORM\Column(name="chartRank3", type="integer", nullable=false)
     */
    private int $chartRank3;

    /**
     * @ORM\Column(name="chartRank4", type="integer", nullable=false)
     */
    private int $chartRank4;

    /**
     * @ORM\Column(name="chartRank5", type="integer", nullable=false)
     */
    private int $chartRank5;

    /**
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private int $pointChart;

    /**
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private int $nbChart;

    /**
     * @ORM\Column(name="nbChartProven", type="integer", nullable=false)
     */
    private int $nbChartProven;

    /**
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=true)
     */
    private DateTime $lastUpdate;

    /**
     * Set rankPointChart
     * @param integer $rankPointChart
     * @return $this
     */
    public function setRankPointChart(int $rankPointChart): Self
    {
        $this->rankPointChart = $rankPointChart;
        return $this;
    }

    /**
     * Get rankPointChart
     *
     * @return integer
     */
    public function getRankPointChart(): int
    {
        return $this->rankPointChart;
    }

    /**
     * Set rankMedal
     * @param integer $rankMedal
     * @return $this
     */
    public function setRankMedal(int $rankMedal): Self
    {
        $this->rankMedal = $rankMedal;
        return $this;
    }

    /**
     * Get rankMedal
     *
     * @return integer
     */
    public function getRankMedal(): int
    {
        return $this->rankMedal;
    }

    /**
     * Set chartRank0
     * @param integer $chartRank0
     * @return $this
     */
    public function setChartRank0(int $chartRank0): Self
    {
        $this->chartRank0 = $chartRank0;
        return $this;
    }

    /**
     * Get chartRank0
     *
     * @return integer
     */
    public function getChartRank0(): int
    {
        return $this->chartRank0;
    }

    /**
     * Set chartRank1
     * @param integer $chartRank1
     * @return $this
     */
    public function setChartRank1(int $chartRank1): Self
    {
        $this->chartRank1 = $chartRank1;
        return $this;
    }

    /**
     * Get chartRank1
     *
     * @return integer
     */
    public function getChartRank1(): int
    {
        return $this->chartRank1;
    }

    /**
     * Set chartRank2
     * @param integer $chartRank2
     * @return $this
     */
    public function setChartRank2(int $chartRank2): Self
    {
        $this->chartRank2 = $chartRank2;
        return $this;
    }

    /**
     * Get chartRank2
     *
     * @return integer
     */
    public function getChartRank2(): int
    {
        return $this->chartRank2;
    }

    /**
     * Set chartRank3
     * @param integer $chartRank3
     * @return $this
     */
    public function setChartRank3(int $chartRank3): Self
    {
        $this->chartRank3 = $chartRank3;
        return $this;
    }

    /**
     * Get chartRank3
     *
     * @return integer
     */
    public function getChartRank3(): int
    {
        return $this->chartRank3;
    }

    /**
     * Set chartRank4
     * @param integer $chartRank4
     * @return $this
     */
    public function setChartRank4(int $chartRank4): Self
    {
        $this->chartRank4 = $chartRank4;
        return $this;
    }

    /**
     * Get chartRank4
     *
     * @return integer
     */
    public function getChartRank4(): int
    {
        return $this->chartRank4;
    }

    /**
     * Set chartRank5
     * @param integer $chartRank5
     * @return $this
     */
    public function setChartRank5(int $chartRank5): Self
    {
        $this->chartRank5 = $chartRank5;
        return $this;
    }

    /**
     * Get chartRank5
     *
     * @return integer
     */
    public function getChartRank5(): int
    {
        return $this->chartRank5;
    }

    /**
     * Set pointChart
     * @param integer $pointChart
     * @return $this
     */
    public function setPointChart(int $pointChart): Self
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return integer
     */
    public function getPointChart(): int
    {
        return $this->pointChart;
    }

    /**
     * Set nbChart
     * @param integer $nbChart
     * @return $this
     */
    public function setNbChart(int $nbChart): Self
    {
        $this->nbChart = $nbChart;
        return $this;
    }

    /**
     * Get nbChart
     *
     * @return integer
     */
    public function getNbChart(): int
    {
        return $this->nbChart;
    }

    /**
     * Set nbChartProven
     * @param integer $nbChartProven
     * @return $this
     */
    public function setNbChartProven(int $nbChartProven): Self
    {
        $this->nbChartProven = $nbChartProven;
        return $this;
    }

    /**
     * Get nbChartProven
     *
     * @return integer
     */
    public function getNbChartProven(): int
    {
        return $this->nbChartProven;
    }

    /**
     * Set lastUpdate
     * @param DateTime $lastUpdate
     * @return $this
     */
    public function setLastUpdate(DateTime $lastUpdate): Self
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
    public function setGroup(Group $group): Self
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
    public function setPlayer(Player $player): Self
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

    /**
     * @ORM\PrePersist
     */
    public function preInsert()
    {
        $this->setRankMedal(0);
    }
}
