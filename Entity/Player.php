<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Sluggable\Sluggable;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Entity\Team;

/**
 * Player
 *
 * @ORM\Table(name="vgr_player", indexes={@ORM\Index(name="pointGame", columns={"pointGame"}), @ORM\Index(name="rank_pointGame", columns={"rankPointGame"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerRepository")
 */
class Player
{
    use Sluggable;

    /**
     * @var \VideoGamesRecords\CoreBundle\Entity\User\UserInterface
     *
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\User\UserInterface")
     * @ORM\JoinColumn(name="normandie_user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\Length(min="3",max="50")
     * @ORM\Column(name="pseudo", type="string", length=50, nullable=false, unique=true)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=100, nullable=false)
     */
    private $avatar = 'default.jpg';

    /**
     * @var string
     *
     * @ORM\Column(name="gamerCard", type="string", length=50, nullable=true)
     */
    private $gamerCard;

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
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointVGR", type="integer", nullable=false)
     */
    private $pointVGR = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointBadge", type="integer", nullable=false)
     */
    private $pointBadge = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="collection", type="text", length=65535, nullable=true)
     */
    private $collection;

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
     * @ORM\Column(name="rankProof", type="integer", nullable=true)
     */
    private $rankProof;

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
     * @ORM\Column(name="rankCountry", type="integer", nullable=true)
     */
    private $rankCountry;

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
     * @ORM\Column(name="nbGame", type="integer", nullable=false)
     */
    private $nbGame = 0;

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
     * @ORM\Column(name="nbChartDisabled", type="integer", nullable=false)
     */
    private $nbChartDisabled = 0;

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
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerGame", mappedBy="player")
     */
    private $playerGame;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerGroup", mappedBy="player")
     */
    private $playerGroup;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerBadge", mappedBy="player")
     */
    private $playerBadge;

    /**
     * @var Team
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="players")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id")
     * })
     */
    private $team;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="ProjetNormandie\CountryBundle\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCountry", referencedColumnName="id")
     * })
     */
    protected $country;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getPseudo(), $this->getId());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Player
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     * @return Player
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return Player
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set gamerCard
     *
     * @param string $gamerCard
     * @return Player
     */
    public function setGamerCard($gamerCard)
    {
        $this->gamerCard = $gamerCard;

        return $this;
    }

    /**
     * Get gamerCard
     *
     * @return string
     */
    public function getGamerCard()
    {
        return $this->gamerCard;
    }

    /**
     * Set chartRank0
     *
     * @param integer $chartRank0
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * Set pointVGR
     *
     * @param integer $pointVGR
     * @return Player
     */
    public function setPointVGR($pointVGR)
    {
        $this->pointVGR = $pointVGR;

        return $this;
    }

    /**
     * Get pointVGR
     *
     * @return integer
     */
    public function getPointVGR()
    {
        return $this->pointVGR;
    }

    /**
     * Set pointBadge
     *
     * @param integer $pointBadge
     * @return Player
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
     * Set collection
     *
     * @param string $collection
     * @return Player
     */
    public function setCollection($collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return string
     */
    public function getCollection()
    {
        return $this->collection;
    }

    /**
     * Set rankPointChart
     *
     * @param integer $rankPointChart
     * @return Player
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
     * @return Player
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
     * Set rankProof
     *
     * @param integer $rankProof
     * @return Player
     */
    public function setRankProof($rankProof)
    {
        $this->rankProof = $rankProof;

        return $this;
    }

    /**
     * Get rankProof
     *
     * @return integer
     */
    public function getRankProof()
    {
        return $this->rankProof;
    }

    /**
     * Set rankBadge
     *
     * @param integer $rankBadge
     * @return Player
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
     * @return Player
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
     * Set rankCountry
     *
     * @param integer $rankCountry
     * @return Player
     */
    public function setRankCountry($rankCountry)
    {
        $this->rankCountry = $rankCountry;

        return $this;
    }

    /**
     * Get rankCountry
     *
     * @return integer
     */
    public function getRankCountry()
    {
        return $this->rankCountry;
    }

    /**
     * Set gameRank0
     *
     * @param integer $gameRank0
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * Set nbGame
     *
     * @param integer $nbGame
     * @return Player
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
     * Set nbChart
     *
     * @param integer $nbChart
     * @return Player
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
     * @return Player
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
     * Set nbChartDisabled
     *
     * @param integer $nbChartDisabled
     * @return Player
     */
    public function setNbChartDisabled($nbChartDisabled)
    {
        $this->nbChartDisabled = $nbChartDisabled;

        return $this;
    }

    /**
     * Get nbChartDisabled
     *
     * @return integer
     */
    public function getNbChartDisabled()
    {
        return $this->nbChartDisabled;
    }

    /**
     * Set nbMasterBadge
     *
     * @param integer $nbMasterBadge
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return \VideoGamesRecords\CoreBundle\Entity\User\UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \VideoGamesRecords\CoreBundle\Entity\User\UserInterface $user
     * @return $this
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlayerGame()
    {
        return $this->playerGame;
    }

    /**
     * @return mixed
     */
    public function getPlayerBadge()
    {
        return $this->playerBadge;
    }

    /**
     * Set team
     * @param Team $team
     * @return Player
     */
    public function setTeam(Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     * @return Team
     */
    public function getTeam()
    {
        return $this->team;
    }

    /**
     * @return $this
     */
    public function getPlayer()
    {
        return $this;
    }

    /**
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @param Country $country
     * @return Player
     */
    public function setCountry($country)
    {
        $this->country = $country;
        return $this;
    }

    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return array
     */
    public function getSluggableFields()
    {
        return ['pseudo'];
    }

    /**
     * @return bool
     */
    public function isLeader()
    {
        return ($this->getTeam() !== null) && ($this->getTeam()->getIdLeader() === $this->getId());
    }
}
