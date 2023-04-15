<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Entity\AverageChartRankTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\AverageGameRankTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankCupTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointBadgeTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointGameTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointChartTrait;

/**
 * @ORM\Table(name="vgr_team")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\TeamRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\TeamListener"})
 * @ApiResource(attributes={"order"={"libTeam"}})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "libTeam": "partial"
 *      }
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *         "parameterName": "groups",
 *         "overrideDefaultGroups": false,
 *         "whitelist": {
 *             "team.rank.pointChart",
 *             "team.rank.pointGame",
 *             "team.rank.medal",
 *             "team.rank.cup",
 *             "team.rank.badge",
 *             "team.players"
 *         }
 *     }
 * )
 */
class Team implements SluggableInterface, TimestampableInterface
{
    use TimestampableTrait;
    use SluggableTrait;
    use RankCupTrait;
    use RankMedalTrait;
    use RankPointBadgeTrait;
    use RankPointGameTrait;
    use RankPointChartTrait;
    use AverageChartRankTrait;
    use AverageGameRankTrait;

    const STATUS_OPENED = 'OPENED';
    const STATUS_CLOSED = 'CLOSED';

    const NUM_ITEMS = 20;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="50")
     * @ORM\Column(name="libTeam", type="string", length=50, nullable=false)
     */
    private string $libTeam;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="4")
     * @ORM\Column(name="tag", type="string", length=10, nullable=false)
     */
    private string $tag;

    /**
     * @Assert\Length(max="255")
     * @Assert\Url(
     *    protocols = {"http", "https"}
     * )
     * @ORM\Column(name="siteWeb", type="string", length=255, nullable=true)
     */
    private ?string $siteWeb;

    /**
     * @ORM\Column(name="logo", type="string", length=30, nullable=false)
     */
    private string $logo = 'default.png';

    /**
     * @ORM\Column(name="presentation", type="text", nullable=true)
     */
    private ?string $presentation;

    /**
     * @Assert\Choice({"CLOSED", "OPENED"})
     * @ORM\Column(name="status", type="string", length=30, nullable=false)
     */
    private string $status = self::STATUS_CLOSED;

    /**
     * @ORM\Column(name="nbPlayer", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbPlayer = 0;

    /**
     * @ORM\Column(name="nbGame", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbGame = 0;

    /**
     * @ORM\Column(name="nbMasterBadge", type="integer", nullable=false, options={"default" : 0})
     */
    private int $nbMasterBadge = 0;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", mappedBy="team")
     * @ORM\OrderBy({"pseudo" = "ASC"})
     */
    private Collection $players;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\TeamGame", mappedBy="team")
     */
    private Collection $teamGame;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\TeamBadge", mappedBy="team")
     */
    private Collection $teamBadge;

    /**
     * @var Player
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLeader", referencedColumnName="id", nullable=false)
     * })
     */
    private Player $leader;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->teamGame = new ArrayCollection();
        $this->teamBadge = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibTeam(), $this->id);
    }


    /**
     * Set id
     * @param integer $id
     * @return Team
     */
    public function setId(int $id): Team
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set libTeam
     * @param string $libTeam
     * @return Team
     */
    public function setLibTeam(string $libTeam): Team
    {
        $this->libTeam = $libTeam;

        return $this;
    }

    /**
     * Get libTeam
     *
     * @return string
     */
    public function getLibTeam(): string
    {
        return $this->libTeam;
    }

    /**
     * Set tag
     * @param string $tag
     * @return Team
     */
    public function setTag(string $tag): Team
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag(): string
    {
        return $this->tag;
    }

    /**
     * Set leader
     * @param Player $leader
     * @return Team
     */
    public function setLeader(Player $leader): Team
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get leader
     * @return Player
     */
    public function getLeader(): Player
    {
        return $this->leader;
    }

    /**
     * Set siteWeb
     * @param string|null $siteWeb
     * @return Team
     */
    public function setSiteWeb(?string $siteWeb): Team
    {
        $this->siteWeb = $siteWeb;

        return $this;
    }

    /**
     * Get siteWeb
     *
     * @return string
     */
    public function getSiteWeb(): ?string
    {
        return $this->siteWeb;
    }

    /**
     * Set logo
     * @param string $logo
     * @return Team
     */
    public function setLogo(string $logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * Set presentation
     * @param string $presentation
     * @return Team
     */
    public function setPresentation(string $presentation): Team
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    /**
     * Set status
     * @param string $status
     * @return Team
     */
    public function setStatus(string $status): Team
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set nbPlayer
     * @param integer $nbPlayer
     * @return Team
     */
    public function setNbPlayer(int $nbPlayer): Team
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     *
     * @return integer
     */
    public function getNbPlayer(): int
    {
        return $this->nbPlayer;
    }

    /**
     * Set nbGame
     * @param integer $nbGame
     * @return Team
     */
    public function setNbGame(int $nbGame): Team
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
     * Set nbMasterBadge
     * @param integer $nbMasterBadge
     * @return Team
     */
    public function setNbMasterBadge(int $nbMasterBadge): Team
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
     * @return Collection
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    /**
     * @return Collection
     */
    public function getTeamGame(): Collection
    {
        return $this->teamGame;
    }

    /**
     * @return Collection
     */
    public function getTeamBadge(): Collection
    {
        return $this->teamBadge;
    }

    /**
     * @return bool
     */
    public function isOpened(): bool
    {
        return ($this->getStatus() == self::STATUS_OPENED);
    }

    /**
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['libTeam'];
    }

    /**
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_CLOSED => self::STATUS_CLOSED,
            self::STATUS_OPENED => self::STATUS_OPENED,
        ];
    }
}
