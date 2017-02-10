<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerSerie
 *
 * @ORM\Table(name="vgr_player_serie", indexes={@ORM\Index(name="idxIdSerie", columns={"idSerie"}), @ORM\Index(name="idxIdPlayer", columns={"idPlayer"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerSerieRepository")
 */
class PlayerSerie
{
    /**
     * @ORM\Column(name="idPlayer", type="integer")
     * @ORM\Id
     */
    private $idPlayer;

    /**
     * @ORM\Column(name="idSerie", type="integer")
     * @ORM\Id
     */
    private $idSerie;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="idPlayer")
     * })
     */
    private $player;

    /**
     * @var Serie
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="idSerie")
     * })
     */
    private $serie;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankPoint", type="integer", nullable=false)
     */
    private $rankPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private $rankMedal;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank0", type="integer", nullable=false)
     */
    private $rank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank1", type="integer", nullable=false)
     */
    private $rank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank2", type="integer", nullable=false)
     */
    private $rank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank3", type="integer", nullable=false)
     */
    private $rank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank4", type="integer", nullable=false)
     */
    private $rank4;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank5", type="integer", nullable=false)
     */
    private $rank5;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private $pointChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChartWithoutDlc", type="integer", nullable=false)
     */
    private $pointChartWithoutDlc;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private $nbChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChartProven", type="integer", nullable=false)
     */
    private $nbChartProven;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChartWithoutDlc", type="integer", nullable=false)
     */
    private $nbChartWithoutDlc;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChartProvenWithoutDlc", type="integer", nullable=false)
     */
    private $nbChartProvenWithoutDlc;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointGame", type="integer", nullable=false)
     */
    private $pointGame;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbGame", type="integer", nullable=false)
     */
    private $nbGame;


    /**
     * Set idPlayer
     *
     * @param integer $idPlayer
     * @return PlayerSerie
     */
    public function setIdPlayer($idPlayer)
    {
        $this->idPlayer = $idPlayer;
        return $this;
    }

    /**
     * Get idPlayer
     *
     * @return integer
     */
    public function getIdPlayer()
    {
        return $this->idPlayer;
    }

    /**
     * Set idSerie
     *
     * @param integer $idSerie
     * @return PlayerSerie
     */
    public function setIdSerie($idSerie)
    {
        $this->idSerie = $idSerie;
        return $this;
    }

    /**
     * Get idSerie
     *
     * @return integer
     */
    public function getIdSerie()
    {
        return $this->idSerie;
    }

    /**
     * Set rankPoint
     *
     * @param integer $rankPoint
     * @return PlayerSerie
     */
    public function setRankPoint($rankPoint)
    {
        $this->rankPoint = $rankPoint;
        return $this;
    }

    /**
     * Get rankPoint
     *
     * @return integer
     */
    public function getRankPoint()
    {
        return $this->rankPoint;
    }

    /**
     * Set rankMedal
     *
     * @param integer $rankMedal
     * @return PlayerSerie
     */
    public function setRankMedal($rankMedal)
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
     * Set rank0
     *
     * @param integer $rank0
     * @return PlayerSerie
     */
    public function setRank0($rank0)
    {
        $this->rank0 = $rank0;
        return $this;
    }

    /**
     * Get rank0
     *
     * @return integer
     */
    public function getRank0()
    {
        return $this->rank0;
    }

    /**
     * Set rank1
     *
     * @param integer $rank1
     * @return PlayerSerie
     */
    public function setRank1($rank1)
    {
        $this->rank1 = $rank1;
        return $this;
    }

    /**
     * Get rank1
     *
     * @return integer
     */
    public function getRank1()
    {
        return $this->rank1;
    }

    /**
     * Set rank2
     *
     * @param integer $rank2
     * @return PlayerSerie
     */
    public function setRank2($rank2)
    {
        $this->rank2 = $rank2;
        return $this;
    }

    /**
     * Get rank2
     *
     * @return integer
     */
    public function getRank2()
    {
        return $this->rank2;
    }

    /**
     * Set rank3
     *
     * @param integer $rank3
     * @return PlayerSerie
     */
    public function setRank3($rank3)
    {
        $this->rank3 = $rank3;
        return $this;
    }

    /**
     * Get rank3
     *
     * @return integer
     */
    public function getRank3()
    {
        return $this->rank3;
    }

    /**
     * Set rank4
     *
     * @param integer $rank4
     * @return PlayerSerie
     */
    public function setRank4($rank4)
    {
        $this->rank4 = $rank4;
        return $this;
    }

    /**
     * Get rank4
     *
     * @return integer
     */
    public function getRank4()
    {
        return $this->rank4;
    }

    /**
     * Set rank5
     *
     * @param integer $rank5
     * @return PlayerSerie
     */
    public function setRank5($rank5)
    {
        $this->rank5 = $rank5;
        return $this;
    }

    /**
     * Get rank5
     *
     * @return integer
     */
    public function getRank5()
    {
        return $this->rank5;
    }

    /**
     * Set pointChart
     *
     * @param integer $pointChart
     * @return PlayerSerie
     */
    public function setPointChart($pointChart)
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
     * Set pointChartWithoutDlc
     *
     * @param integer $pointChartWithoutDlc
     * @return PlayerSerie
     */
    public function setPointChartWithoutDlc($pointChartWithoutDlc)
    {
        $this->pointChartWithoutDlc = $pointChartWithoutDlc;
        return $this;
    }

    /**
     * Get pointChartWithoutDlc
     *
     * @return integer
     */
    public function getPointChartWithoutDlc()
    {
        return $this->pointChartWithoutDlc;
    }


    /**
     * Set nbChart
     *
     * @param integer $nbChart
     * @return PlayerSerie
     */
    public function setNbChart($nbChart)
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
     *
     * @param integer $nbChartProven
     * @return PlayerSerie
     */
    public function setNbChartProven($nbChartProven)
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
     * Set nbChartWithoutDlc
     *
     * @param integer $nbChartWithoutDlc
     * @return PlayerSerie
     */
    public function setNbChartWithoutDlc($nbChartWithoutDlc)
    {
        $this->nbChartWithoutDlc = $nbChartWithoutDlc;
        return $this;
    }

    /**
     * Get nbChartWithoutDlc
     *
     * @return integer
     */
    public function getNbChartWithoutDlc()
    {
        return $this->nbChartWithoutDlc;
    }


    /**
     * Set nbChartProvenWithoutDlc
     *
     * @param integer $nbChartProvenWithoutDlc
     * @return PlayerSerie
     */
    public function setNbChartProvenWithoutDlc($nbChartProvenWithoutDlc)
    {
        $this->nbChartProvenWithoutDlc = $nbChartProvenWithoutDlc;
        return $this;
    }

    /**
     * Get nbChartProvenWithoutDlc
     *
     * @return integer
     */
    public function getNbChartProvenWithoutDlc()
    {
        return $this->nbChartProvenWithoutDlc;
    }

    /**
     * Set pointGame
     *
     * @param integer $pointGame
     * @return PlayerSerie
     */
    public function setPointGame($pointGame)
    {
        $this->pointGame = $pointGame;
        return $this;
    }

    /**
     * Get pointGame
     *
     * @return integer
     */
    public function getPointGame()
    {
        return $this->pointGame;
    }


    /**
     * Set nbGame
     *
     * @param integer $nbGame
     * @return PlayerSerie
     */
    public function setNbGame($nbGame)
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
     * Set serie
     *
     * @param Serie $serie
     * @return PlayerSerie
     */
    public function setSerie(Serie $serie = null)
    {
        $this->serie = $serie;
        $this->setIdSerie($serie->getIdSerie());
        return $this;
    }

    /**
     * Get serie
     *
     * @return Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }


    /**
     * Set player
     *
     * @param Player $player
     * @return PlayerSerie
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;
        $this->setIdPlayer($player->getIdPlayer());
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


    /**
     * @return string
     */
    public function getPointsBackgroundColor()
    {
        $class = array(
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        );

        if ($this->getRankPoint() <= 3) {
            return sprintf("class=\"%s\"", $class[$this->getRankPoint()]);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getMedalsBackgroundColor()
    {
        $class = array(
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        );

        if ($this->getRankMedal() <= 3) {
            return sprintf("class=\"%s\"", $class[$this->getRankMedal()]);
        } else {
            return '';
        }
    }
}
