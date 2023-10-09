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
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Traits\Entity\AverageChartRankTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\AverageGameRankTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbVideoTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerCommunicationDataTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerPersonalDataTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankCupTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointBadgeTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointGameTrait;

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
 *              "player.user_id",
 *              "team.read.mini",
 *              "player.status",
 *              "player.status.read"
 *          }
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "pseudo" : "ASC",
 *          "createdAt": "DESC",
 *          "nbConnexion": "DESC",
 *          "lastLogin": "DESC",
 *          "nbForumMessage": "DESC",
 *          "nbChart": "DESC",
 *          "nbVideo": "DESC"
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class Player implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
    use RankCupTrait;
    use RankMedalTrait;
    use RankPointBadgeTrait;
    use RankPointChartTrait;
    use RankPointGameTrait;
    use AverageChartRankTrait;
    use AverageGameRankTrait;
    use PlayerCommunicationDataTrait;
    use PlayerPersonalDataTrait;
    use NbChartTrait;
    use NbChartProvenTrait;
    use NbGameTrait;
    use NbVideoTrait;

    /**
     * @ORM\Column(name="user_id", type="integer")
     */
    private int $user_id;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
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
     * @ORM\Column(name="rankProof", type="integer", nullable=false, options={"default" : 0})
     */
    private int $rankProof = 0;

    /**
     * @ORM\Column(name="rankCountry", type="integer", nullable=false, options={"default" : 0})
     */
    private int $rankCountry = 0;

    /**
     * @ORM\Column(name="nbChartMax", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChartMax = 0;

    /**
     * @ORM\Column(name="nbChartWithPlatform", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChartWithPlatform = 0;

    /**
     * @ORM\Column(name="nbChartDisabled", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbChartDisabled = 0;

    /**
     * @ORM\Column(name="nbMasterBadge", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbMasterBadge = 0;

    /**
     * @ORM\Column(name="last_login",type="datetime", nullable=true)
     */
    protected ?DateTime $lastLogin = null;

    /**
     * @ORM\Column(name="nbConnexion", type="integer", nullable=false)
     */
    protected int $nbConnexion = 0;

    /**
     * @ORM\Column(name="boolMaj", type="boolean", nullable=false, options={"default":0})
     */
    private bool $boolMaj = false;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Team", inversedBy="players")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTeam", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private ?Team $team;

    /**
     * @ORM\Column(name="lastDisplayLostPosition", type="datetime", nullable=true)
     */
    protected ?DateTime $lastDisplayLostPosition;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatus", referencedColumnName="id", nullable=false)
     * })
     */
    private PlayerStatus $status;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Proof", mappedBy="playerResponding")
     */
    private $proofRespondings;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerGame", mappedBy="player")
     */
    private $playerGame;


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
    public function setId(int $id): Player
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     * @return int|null
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
    public function setPseudo(string $pseudo): Player
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
    public function setAvatar(string $avatar): Player
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
    public function setGamerCard(string $gamerCard): Player
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
     * Set rankProof
     *
     * @param integer $rankProof
     * @return Player
     */
    public function setRankProof(int $rankProof): Player
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
    public function setRankCountry(int $rankCountry): Player
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
     * Set nbChartMax
     *
     * @param integer $nbChartMax
     * @return Player
     */
    public function setNbChartMax(int $nbChartMax): Player
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
    public function setNbChartWithPlatform(int $nbChartWithPlatform): Player
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
     * Set nbChartDisabled
     *
     * @param integer $nbChartDisabled
     * @return Player
     */
    public function setNbChartDisabled(int $nbChartDisabled): Player
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
    public function setNbMasterBadge(int $nbMasterBadge): Player
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
     * @return DateTime|null
     */
    public function getLastLogin(): ?DateTime
    {
        return $this->lastLogin;
    }

    /**
     * @param DateTime|null $time
     * @return Player
     */
    public function setLastLogin(DateTime $time = null) : Player
    {
        $this->lastLogin = $time;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param $userId
     * @return Player
     */
    public function setUserId($userId): Player
    {
        $this->user_id = $userId;
        return $this;
    }


    /**
     * Set Team
     * @param Team|null $team
     * @return $this
     */
    public function setTeam(Team $team = null): Player
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
    public function setLastDisplayLostPosition(DateTime $lastDisplayLostPosition = null): Player
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
    public function setBoolMaj(bool $boolMaj): Player
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
     * Set status
     * @param PlayerStatus $status
     * @return Player
     */
    public function setStatus(PlayerStatus $status): Player
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     * @return PlayerStatus
     */
    public function getStatus(): PlayerStatus
    {
        return $this->status;
    }

    /**
     * @return int
     */
    public function getNbConnexion(): int
    {
        return $this->nbConnexion;
    }

    /**
     * @param int $nbConnexion
     * @return Player
     */
    public function setNbConnexion(int $nbConnexion): Player
    {
        $this->nbConnexion = $nbConnexion;
        return $this;
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
