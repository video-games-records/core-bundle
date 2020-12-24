<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerPlatform
 *
 * @ORM\Table(name="vgr_player_platform")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerPlatformRepository")
 */
class PlayerPlatform
{
    /**
     * @var Player
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="playerPlatform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private $player;

    /**
     * @var Platform
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform", fetch="EAGER", inversedBy="playerPlatform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlatform", referencedColumnName="id", nullable=false)
     * })
     */
    private $platform;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankPointChart", type="integer", nullable=false)
     */
    private $rankPointChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private $rankMedal;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank0", type="integer", nullable=false)
     */
    private $chartRank0 = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank1", type="integer", nullable=false)
     */
    private $chartRank1 = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank2", type="integer", nullable=false)
     */
    private $chartRank2 = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank3", type="integer", nullable=false)
     */
    private $chartRank3 = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank4", type="integer", nullable=false)
     */
    private $chartRank4 = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank5", type="integer", nullable=false)
     */
    private $chartRank5 = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private $nbChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChartProven", type="integer", nullable=false)
     */
    private $nbChartProven = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbGame", type="integer", nullable=false)
     */
    private $nbGame = 0;

    /**
     * Set rankPointChart
     * @param integer $rankPointChart
     * @return $this
     */
    public function setRankPointChart(int $rankPointChart)
    {
        $this->rankPointChart = $rankPointChart;
        return $this;
    }

    /**
     * Get rankPointChart
     *
     * @return integer
     */
    public function getRankPointChart()
    {
        return $this->rankPointChart;
    }

    /**
     * Set rankMedal
     * @param integer $rankMedal
     * @return $this
     */
    public function setRankMedal(int $rankMedal)
    {
        $this->rankMedal = $rankMedal;
        return $this;
    }

    /**
     * Get rankMedal
     *
     * @return integer
     */
    public function getRankMedal()
    {
        return $this->rankMedal;
    }

    /**
     * Set chartRank0
     * @param integer $chartRank0
     * @return $this
     */
    public function setChartRank0(int $chartRank0)
    {
        $this->chartRank0 = $chartRank0;
        return $this;
    }

    /**
     * Get chartRank0
     *
     * @return integer
     */
    public function getChartRank0()
    {
        return $this->chartRank0;
    }

    /**
     * Set chartRank1
     * @param integer $chartRank1
     * @return $this
     */
    public function setChartRank1(int $chartRank1)
    {
        $this->chartRank1 = $chartRank1;
        return $this;
    }

    /**
     * Get chartRank1
     *
     * @return integer
     */
    public function getChartRank1()
    {
        return $this->chartRank1;
    }

    /**
     * Set chartRank2
     * @param integer $chartRank2
     * @return $this
     */
    public function setChartRank2(int $chartRank2)
    {
        $this->chartRank2 = $chartRank2;
        return $this;
    }

    /**
     * Get chartRank2
     *
     * @return integer
     */
    public function getChartRank2()
    {
        return $this->chartRank2;
    }

    /**
     * Set chartRank3
     * @param integer $chartRank3
     * @return $this
     */
    public function setChartRank3(int $chartRank3)
    {
        $this->chartRank3 = $chartRank3;
        return $this;
    }

    /**
     * Get chartRank3
     *
     * @return integer
     */
    public function getChartRank3()
    {
        return $this->chartRank3;
    }

    /**
     * Set chartRank4
     * @param integer $chartRank4
     * @return $this
     */
    public function setChartRank4(int $chartRank4)
    {
        $this->chartRank4 = $chartRank4;
        return $this;
    }

    /**
     * Get chartRank4
     *
     * @return integer
     */
    public function getChartRank4()
    {
        return $this->chartRank4;
    }

    /**
     * Set chartRank5
     * @param integer $chartRank5
     * @return $this
     */
    public function setChartRank5(int $chartRank5)
    {
        $this->chartRank5 = $chartRank5;
        return $this;
    }

    /**
     * Get chartRank5
     *
     * @return integer
     */
    public function getChartRank5()
    {
        return $this->chartRank5;
    }

    /**
     * Set pointChart
     * @param integer $pointChart
     * @return $this
     */
    public function setPointChart(int $pointChart)
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return integer
     */
    public function getPointChart()
    {
        return $this->pointChart;
    }

    /**
     * Set nbChart
     * @param integer $nbChart
     * @return $this
     */
    public function setNbChart(int $nbChart)
    {
        $this->nbChart = $nbChart;
        return $this;
    }

    /**
     * Get nbChart
     *
     * @return integer
     */
    public function getNbChart()
    {
        return $this->nbChart;
    }


    /**
     * Set nbChartProven
     * @param integer $nbChartProven
     * @return $this
     */
    public function setNbChartProven(int $nbChartProven)
    {
        $this->nbChartProven = $nbChartProven;
        return $this;
    }

    /**
     * Get nbChartProven
     *
     * @return integer
     */
    public function getNbChartProven()
    {
        return $this->nbChartProven;
    }


    /**
     * @param int $nbGame
     * @return $this
     */
    public function setNbGame(int $nbGame)
    {
        $this->nbGame = $nbGame;
        return $this;
    }

    /**
     * Get nbGame
     *
     * @return integer
     */
    public function getNbGame()
    {
        return $this->nbGame;
    }

    /**
     * Set platform
     * @param Platform|null $platform
     * @return $this
     */
    public function setPlatform(Platform $platform = null)
    {
        $this->platform = $platform;

        return $this;
    }


    /**
     * Get latform
     *
     * @return Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }


    /**
     * Set player
     * @param Player|null $player
     * @return $this
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
