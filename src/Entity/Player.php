<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Entity\UserInterface;
use VideoGamesRecords\CoreBundle\Model\Entity\AverageChartRankTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\AverageGameRankTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankCupTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointBadgeTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointChartTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointGameTrait;

/**
 * Player
 *
* @ORM\Table(
 *     name="vgr_player",
 *     indexes={
 *         @ORM\Index(name="idx_pointGame", columns={"pointGame"}),
 *         @ORM\Index(name="idx_chartRank", columns={"chartRank0", "chartRank1", "chartRank2", "chartRank3"}),
 *         @ORM\Index(name="idx_gameRank", columns={"gameRank0", "gameRank1", "gameRank2", "gameRank3"}),
 *     }
 * )
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\PlayerListener"})
 * @ApiResource(attributes={"order"={"pseudo"}})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "pseudo": "partial",
 *          "user.enabled": "exact",
 *      }
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {
 *              "player.read",
 *              "player.team",
 *              "player.country",
 *              "country.read",
 *              "player.pointChart",
 *              "player.medal",
 *              "player.user",
 *              "vgr.user.read",
 *              "team.read.mini",
 *              "user.status.read",
 *          }
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "pseudo" : "ASC",
 *          "user.createdAt": "DESC",
 *          "user.nbConnexion": "DESC",
 *          "user.lastLogin": "DESC",
 *          "user.nbForumMessage": "DESC"
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class Player implements SluggableInterface
{
    use SluggableTrait;
    use RankCupTrait;
    use RankMedalTrait;
    use RankPointBadgeTrait;
    use RankPointChartTrait;
    use RankPointGameTrait;
    use AverageChartRankTrait;
    use AverageGameRankTrait;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\UserInterface")
     * @ORM\JoinColumn(name="normandie_user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\Length(min="3",max="50")
     * @ORM\Column(name="pseudo", type="string", length=50, nullable=false, unique=true)
     */
    private string $pseudo;

    /**
     * @ORM\Column(name="avatar", type="string", length=100, nullable=false, options={"default":"default.jpg"})
     */
    private string $avatar = 'default.jpg';

    /**
     * @ORM\Column(name="gamerCard", type="string", length=50, nullable=true)
     */
    private ?string $gamerCard;

    /**
     * @ORM\Column(name="pointVGR", type="integer", nullable=false, options={"default" : 0})
     */
    private int $pointVGR = 0;

    /**
     * @ORM\Column(name="presentation", type="text", length=65535, nullable=true)
     */
    private ?string $presentation;

    /**
     * @ORM\Column(name="collection", type="text", length=65535, nullable=true)
     */
    private ?string $collection;

    /**
     * @ORM\Column(name="rankProof", type="integer", nullable=false, options={"default" : 0})
     */
    private int $rankProof = 0;

    /**
     * @ORM\Column(name="rankCountry", type="integer", nullable=false, options={"default" : 0})
     */
    private int $rankCountry = 0;

    /**
     * @ORM\Column(name="nbGame", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbGame = 0;

    /**
     * @ORM\Column(name="nbChart", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChart = 0;

    /**
     * @ORM\Column(name="nbChartMax", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChartMax = 0;

    /**
     * @ORM\Column(name="nbChartWithPlatform", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChartWithPlatform = 0;

    /**
     * @ORM\Column(name="nbChartProven", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChartProven = 0;

    /**
     * @ORM\Column(name="nbChartDisabled", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChartDisabled = 0;

    /**
     * @ORM\Column(name="nbMasterBadge", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbMasterBadge = 0;

    /**
     * @ORM\Column(name="birthDate", type="date", nullable=true)
     */
    protected ?DateTime $birthDate;

    /**
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    protected ?string $website;

    /**
     * @ORM\Column(name="youtube", type="string", length=255, nullable=true)
     */
    protected ?string $youtube;

    /**
     * @ORM\Column(name="twitch", type="string", length=255, nullable=true)
     */
    protected ?string $twitch;

    /**
     * @ORM\Column(name="gender", type="string", length=1, nullable=false, options={"default" : "I"}))
     */
    protected string $gender = 'I';

    /**
     * @ORM\Column(name="displayPersonalInfos", type="boolean", nullable=false)
     */
    private bool $displayPersonalInfos = false;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerPlatform", mappedBy="player")
     */
    private $playerPlatform;

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
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\LostPosition", mappedBy="player")
     */
    private $lostPositions;

    /**
     * @ORM\Column(name="boolMaj", type="boolean", nullable=false, options={"default":0})
     */
    private $boolMaj = false;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="players")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private ?Team $team;

    /**
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCountry", referencedColumnName="id")
     * })
     */
    protected $country;


    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Proof", mappedBy="player")
     */
    private $proofs;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Proof", mappedBy="playerResponding")
     */
    private $proofRespondings;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Rule", mappedBy="player")
     */
    private $rules;

    /**
     * @var DateTime
     * @ORM\Column(name="lastDisplayLostPosition", type="datetime", nullable=true)
     */
    protected $lastDisplayLostPosition;

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
     * @return $this
     */
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     * @return $this
     */
    public function setPseudo(string $pseudo): static
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo(): string
    {
        return $this->pseudo;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return $this
     */
    public function setAvatar(string $avatar): static
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar(): string
    {
        return $this->avatar;
    }

    /**
     * Set gamerCard
     *
     * @param string $gamerCard
     * @return $this
     */
    public function setGamerCard(string $gamerCard): static
    {
        $this->gamerCard = $gamerCard;

        return $this;
    }

    /**
     * Get gamerCard
     * @return string|null
     */
    public function getGamerCard(): ?string
    {
        return $this->gamerCard;
    }


    /**
     * Set pointVGR
     *
     * @param integer $pointVGR
     * @return $this
     */
    public function setPointVGR(int $pointVGR): static
    {
        $this->pointVGR = $pointVGR;

        return $this;
    }

    /**
     * Get pointVGR
     *
     * @return integer
     */
    public function getPointVGR(): int
    {
        return $this->pointVGR;
    }

    /**
     * Set presentation
     *
     * @param string|null $presentation
     * @return $this
     */
    public function setPresentation(string $presentation = null): static
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     * @return string|null
     */
    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    /**
     * Set collection
     *
     * @param string|null $collection
     * @return Player
     */
    public function setCollection(string $collection = null): static
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     * @return string|null
     */
    public function getCollection(): ?string
    {
        return $this->collection;
    }

    /**
     * Set rankProof
     *
     * @param integer $rankProof
     * @return Player
     */
    public function setRankProof(int $rankProof): static
    {
        $this->rankProof = $rankProof;

        return $this;
    }

    /**
     * Get rankProof
     * @return int|null
     */
    public function getRankProof(): ?int
    {
        return $this->rankProof;
    }


    /**
     * Set rankCountry
     *
     * @param integer $rankCountry
     * @return Player
     */
    public function setRankCountry(int $rankCountry): static
    {
        $this->rankCountry = $rankCountry;

        return $this;
    }

    /**
     * Get rankCountry
     * @return int|null
     */
    public function getRankCountry(): ?int
    {
        return $this->rankCountry;
    }


    /**
     * Set nbGame
     *
     * @param integer $nbGame
     * @return Player
     */
    public function setNbGame(int $nbGame): static
    {
        $this->nbGame = $nbGame;

        return $this;
    }

    /**
     * Get nbGame
     *
     * @return integer
     */
    public function getNbGame(): int
    {
        return $this->nbGame;
    }

    /**
     * Set nbChart
     *
     * @param integer $nbChart
     * @return Player
     */
    public function setNbChart(int $nbChart): static
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
     * Set nbChartMax
     *
     * @param integer $nbChartMax
     * @return Player
     */
    public function setNbChartMax(int $nbChartMax): static
    {
        $this->nbChartMax = $nbChartMax;

        return $this;
    }

    /**
     * Get nbChartMax
     *
     * @return integer
     */
    public function getNbChartMax(): int
    {
        return $this->nbChartMax;
    }

     /**
     * Set nbChartWithPlatform
     *
     * @param integer $nbChartWithPlatform
     * @return Player
     */
    public function setNbChartWithPlatform(int $nbChartWithPlatform): static
    {
        $this->nbChartWithPlatform = $nbChartWithPlatform;

        return $this;
    }

    /**
     * Get nbChartWithPlatform
     *
     * @return integer
     */
    public function getNbChartWithPlatform(): int
    {
        return $this->nbChartWithPlatform;
    }

    /**
     * Set nbChartProven
     *
     * @param integer $nbChartProven
     * @return Player
     */
    public function setNbChartProven(int $nbChartProven): static
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
     * Set nbChartDisabled
     *
     * @param integer $nbChartDisabled
     * @return Player
     */
    public function setNbChartDisabled(int $nbChartDisabled): static
    {
        $this->nbChartDisabled = $nbChartDisabled;

        return $this;
    }

    /**
     * Get nbChartDisabled
     *
     * @return integer
     */
    public function getNbChartDisabled(): int
    {
        return $this->nbChartDisabled;
    }

    /**
     * Set nbMasterBadge
     *
     * @param integer $nbMasterBadge
     * @return Player
     */
    public function setNbMasterBadge(int $nbMasterBadge): static
    {
        $this->nbMasterBadge = $nbMasterBadge;

        return $this;
    }

    /**
     * Get nbMasterBadge
     *
     * @return integer
     */
    public function getNbMasterBadge(): int
    {
        return $this->nbMasterBadge;
    }


    /**
     * @return string|null
     */
    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $website
     * @return $this
     */
    public function setWebsite(string $website = null): static
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    /**
     * @param string|null $youtube
     * @return $this
     */
    public function setYoutube(string $youtube = null): static
    {
        $this->youtube = $youtube;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    /**
     * @param string|null $twitch
     * @return $this
     */
    public function setTwitch(string $twitch = null): static
    {
        $this->twitch = $twitch;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }


    /**
     * @param DateTime|null $birthDate
     * @return $this
     */
    public function setBirthDate(DateTime $birthDate = null): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }

    /**
     * @param string $gender
     * @return $this
     */
    public function setGender(string $gender): static
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * Set displayPersonalInfos
     * @param bool $displayPersonalInfos
     * @return $this
     */
    public function setDisplayPersonalInfos(bool $displayPersonalInfos): static
    {
        $this->displayPersonalInfos = $displayPersonalInfos;

        return $this;
    }

    /**
     * Get DisplayPersonalInfos
     * @return bool
     */
    public function getDisplayPersonalInfos(): bool
    {
        return $this->displayPersonalInfos;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param $user
     * @return Player
     */
    public function setUser($user): static
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlayerPlatform()
    {
        return $this->playerPlatform;
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
    public function getPlayerBadge(): mixed
    {
        return $this->playerBadge;
    }

    /**
     * Set Team
     * @param Team|null $team
     * @return $this
     */
    public function setTeam(Team $team = null): static
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     * @return Team|null
     */
    public function getTeam(): ?Team
    {
        return $this->team;
    }

    /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * @param $country
     * @return Player
     */
    public function setCountry($country): static
    {
        $this->country = $country;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getLostPositions(): Collection
    {
        return $this->lostPositions;
    }

    /**
     * @return DateTime|null
     */
    public function getLastDisplayLostPosition(): ?DateTime
    {
        return $this->lastDisplayLostPosition;
    }


    /**
     * @param DateTime|null $lastDisplayLostPosition
     * @return $this
     */
    public function setLastDisplayLostPosition(DateTime $lastDisplayLostPosition = null): static
    {
        $this->lastDisplayLostPosition = $lastDisplayLostPosition;
        return $this;
    }

    /**
     * Set boolMaj
     *
     * @param bool $boolMaj
     * @return $this
     */
    public function setBoolMaj(bool $boolMaj): static
    {
        $this->boolMaj = $boolMaj;

        return $this;
    }

    /**
     * Get boolMaj
     *
     * @return bool
     */
    public function getBoolMaj(): bool
    {
        return $this->boolMaj;
    }

    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['pseudo'];
    }

    /**
     * @return bool
     */
    public function isLeader(): bool
    {
        return ($this->getTeam() !== null) && ($this->getTeam()->getLeader()->getId() === $this->getId());
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return sprintf(
            '%s-player-p%d/index',
            $this->getSlug(),
            $this->getId()
        );
    }
}
