<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberGame
 *
 * @ORM\Table(name="vgr_user_game", indexes={@ORM\Index(name="idxIdGame", columns={"idGame"}), @ORM\Index(name="idxIdUser", columns={"idUser"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserGameRepository")
 */
class UserGame
{

    /**
     * @ORM\Column(name="idUser", type="integer")
     * @ORM\Id
     */
    private $idUser;

    /**
     * @ORM\Column(name="idGame", type="integer")
     * @ORM\Id
     */
    private $idGame;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="idUser")
     * })
     */
    private $user;

    /**
     * @var Game
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="idGame")
     * })
     */
    private $game;

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
     * Set idUser
     *
     * @param integer $idUser
     * @return UserGame
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }


    /**
     * Set idGame
     *
     * @param integer $idGame
     * @return UserGame
     */
    public function setIdGame($idGame)
    {
        $this->idGame = $idGame;
        return $this;
    }

    /**
     * Get idGame
     *
     * @return integer
     */
    public function getIdGame()
    {
        return $this->idGame;
    }


    /**
     * Set rankPoint
     *
     * @param integer $rankPoint
     * @return UserGame
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
     * @return UserGame
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
     * Set rank
     *
     * @param integer $rank0
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * @return UserGame
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
     * Set game
     *
     * @param Game $game
     * @return UserGame
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;
        $this->setIdGame($game->getIdGame());
        return $this;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }


    /**
     * Set user
     *
     * @param User $user
     * @return UserGame
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        $this->setIdUser($user->getIdUser());
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
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
