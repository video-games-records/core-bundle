<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="vgr_team")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamRepository")
 */
class Team
{
    const STATUS_OPENED = 'OPENED';
    const STATUS_CLOSED = 'CLOSED';


    /**
     * @var integer
     *
     * @ORM\Column(name="idTeam", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTeam;

    /**
     * @var string
     *
     * @Assert\Length(max="50")
     * @ORM\Column(name="libTeam", type="string", length=50, nullable=false)
     */
    private $libTeam;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=50, nullable=true)
     */
    private $tag;

    /**
     * @var integer
     *
     * @ORM\Column(name="idLeader", type="integer", nullable=false)
     */
    private $idLeader;

    /**
     * @var string
     *
     * @ORM\Column(name="siteWeb", type="string", length=255, nullable=true)
     */
    private $siteWeb;

    /**
     * @var string
     *
     * @ORM\Column(name="logo", type="string", length=30, nullable=false)
     */
    private $logo = 'default.jpg';

    /**
     * @var string
     *
     * @ORM\Column(name="commentaire", type="text", nullable=true)
     */
    private $commentaire;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_CLOSED;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPlayer", type="integer", nullable=true)
     */
    private $nbPlayer;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointBadge", type="integer", nullable=false)
     */
    private $pointBadge = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank0", type="integer", nullable=true)
     */
    private $chartRank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank1", type="integer", nullable=true)
     */
    private $chartRank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank2", type="integer", nullable=true)
     */
    private $chartRank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="chartRank3", type="integer", nullable=true)
     */
    private $chartRank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankPointChart", type="integer", nullable=true)
     */
    private $rankPointChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankMedal", type="integer", nullable=true)
     */
    private $rankMedal;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankBadge", type="integer", nullable=true)
     */
    private $rankBadge;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankCup", type="integer", nullable=true)
     */
    private $rankCup;

    /**
     * @var integer
     *
     * @ORM\Column(name="gameRank0", type="integer", nullable=true)
     */
    private $gameRank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="gameRank1", type="integer", nullable=true)
     */
    private $gameRank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="gameRank2", type="integer", nullable=true)
     */
    private $gameRank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="gameRank3", type="integer", nullable=true)
     */
    private $gameRank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbMasterBadge", type="integer", nullable=false)
     */
    private $nbMasterBadge = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointGame", type="integer", nullable=false)
     */
    private $pointGame = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankPointGame", type="integer", nullable=true)
     */
    private $rankPointGame;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", mappedBy="team")
     */
    private $players;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
    }


    /**
     * Set idTeam
     *
     * @param integer $idTeam
     * @return Team
     */
    public function setIdTeam($idTeam)
    {
        $this->idTeam = $idTeam;
        return $this;
    }

    /**
     * Get idTeam
     *
     * @return integer
     */
    public function getIdTeam()
    {
        return $this->idTeam;
    }

    /**
     * Set libTeam
     *
     * @param string $libTeam
     * @return Team
     */
    public function setLibTeam($libTeam)
    {
        $this->libTeam = $libTeam;

        return $this;
    }

    /**
     * Get libTeam
     *
     * @return string
     */
    public function getLibTeam()
    {
        return $this->libTeam;
    }

    /**
     * Set tag
     *
     * @param string $tag
     * @return Team
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * Set idLeader
     *
     * @param integer $idLeader
     * @return Team
     */
    public function setIdLeader($idLeader)
    {
        $this->idLeader = $idLeader;

        return $this;
    }

    /**
     * Get idLeader
     *
     * @return integer
     */
    public function getIdLeader()
    {
        return $this->idLeader;
    }

    /**
     * Set siteWeb
     *
     * @param string $siteWeb
     * @return Team
     */
    public function setSiteWeb($siteWeb)
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string
     */
    public function getSiteWeb()
    {
        return $this->siteWeb;
    }

    /**
     * Set logo
     *
     * @param string $logo
     * @return Team
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set commentaire
     *
     * @param string $commentaire
     * @return Team
     */
    public function setCommentaire($commentaire)
    {
        $this->commentaire = $commentaire;

        return $this;
    }

    /**
     * Get commentaire
     *
     * @return string
     */
    public function getCommentaire()
    {
        return $this->commentaire;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Team
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set nbPlayer
     *
     * @param integer $nbPlayer
     * @return Team
     */
    public function setNbPlayer($nbPlayer)
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     *
     * @return integer
     */
    public function getNbPlayer()
    {
        return $this->nbPlayer;
    }

    /**
     * Set chartRank0
     *
     * @param integer $chartRank0
     * @return Team
     */
    public function setChartRank0($chartRank0)
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
     *
     * @param integer $chartRank1
     * @return Team
     */
    public function setChartRank1($chartRank1)
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
     *
     * @param integer $chartRank2
     * @return Team
     */
    public function setChartRank2($chartRank2)
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
     *
     * @param integer $chartRank3
     * @return Team
     */
    public function setChartRank3($chartRank3)
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
     * Set pointChart
     *
     * @param integer $pointChart
     * @return Team
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
     * Set pointBadge
     *
     * @param integer $pointBadge
     * @return Team
     */
    public function setPointBadge($pointBadge)
    {
        $this->pointBadge = $pointBadge;

        return $this;
    }

    /**
     * Get pointBadge
     *
     * @return integer
     */
    public function getPointBadge()
    {
        return $this->pointBadge;
    }

    /**
     * Set rankPointChart
     *
     * @param integer $rankPointChart
     * @return Team
     */
    public function setRankPointChart($rankPointChart)
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
     *
     * @param integer $rankMedal
     * @return Team
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
     * Set rankBadge
     *
     * @param integer $rankBadge
     * @return Team
     */
    public function setRankBadge($rankBadge)
    {
        $this->rankBadge = $rankBadge;

        return $this;
    }

    /**
     * Get rankBadge
     *
     * @return integer
     */
    public function getRankBadge()
    {
        return $this->rankBadge;
    }

    /**
     * Set rankCup
     *
     * @param integer $rankCup
     * @return Team
     */
    public function setRankCup($rankCup)
    {
        $this->rankCup = $rankCup;

        return $this;
    }

    /**
     * Get rankCup
     *
     * @return integer
     */
    public function getRankCup()
    {
        return $this->rankCup;
    }

    /**
     * Set gameRank0
     *
     * @param integer $gameRank0
     * @return Team
     */
    public function setGameRank0($gameRank0)
    {
        $this->gameRank0 = $gameRank0;

        return $this;
    }

    /**
     * Get gameRank0
     *
     * @return integer
     */
    public function getgameRank0()
    {
        return $this->gameRank0;
    }

    /**
     * Set gameRank1
     *
     * @param integer $gameRank1
     * @return Team
     */
    public function setGameRank1($gameRank1)
    {
        $this->gameRank1 = $gameRank1;

        return $this;
    }

    /**
     * Get gameRank1
     *
     * @return integer
     */
    public function getGameRank1()
    {
        return $this->gameRank1;
    }

    /**
     * Set gameRank2
     *
     * @param integer $gameRank2
     * @return Team
     */
    public function setGameRank2($gameRank2)
    {
        $this->gameRank2 = $gameRank2;

        return $this;
    }

    /**
     * Get gameRank2
     *
     * @return integer
     */
    public function getGameRank2()
    {
        return $this->gameRank2;
    }

    /**
     * Set gameRank3
     *
     * @param integer $gameRank3
     * @return Team
     */
    public function setGameRank3($gameRank3)
    {
        $this->gameRank3 = $gameRank3;

        return $this;
    }

    /**
     * Get gameRank3
     *
     * @return integer
     */
    public function getGameRank3()
    {
        return $this->gameRank3;
    }


    /**
     * Set nbMasterBadge
     *
     * @param integer $nbMasterBadge
     * @return Team
     */
    public function setNbMasterBadge($nbMasterBadge)
    {
        $this->nbMasterBadge = $nbMasterBadge;

        return $this;
    }

    /**
     * Get nbMasterBadge
     *
     * @return integer
     */
    public function getNbMasterBadge()
    {
        return $this->nbMasterBadge;
    }

    /**
     * Set pointGame
     *
     * @param integer $pointGame
     * @return Team
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
     * Set rankPointGame
     *
     * @param integer $rankPointGame
     * @return Team
     */
    public function setRankPointGame($rankPointGame)
    {
        $this->rankPointGame = $rankPointGame;

        return $this;
    }

    /**
     * Get rankPointGame
     *
     * @return integer
     */
    public function getRankPointGame()
    {
        return $this->rankPointGame;
    }

    /**
     * @return mixed
     */
    public function getPlayers()
    {
        return $this->players;
    }
}