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
use VideoGamesRecords\CoreBundle\Model\Entity\RankCupTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointGameTrait;
use VideoGamesRecords\CoreBundle\Model\Entity\RankPointChartTrait;
/**
 * Team
 *
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
    use RankPointGameTrait;
    use RankPointChartTrait;

    const STATUS_OPENED = 'OPENED';
    const STATUS_CLOSED = 'CLOSED';

    const NUM_ITEMS = 20;

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
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="50")
     * @ORM\Column(name="libTeam", type="string", length=50, nullable=false)
     */
    private $libTeam;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="2", max="4")
     * @ORM\Column(name="tag", type="string", length=10, nullable=false)
     */
    private $tag;

    /**
     * @var string
     *
     * @Assert\Length(max="255")
     * @Assert\Url(
     *    protocols = {"http", "https"}
     * )
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
     * @Assert\Choice({"CLOSED", "OPENED"})
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_CLOSED;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPlayer", type="integer", nullable=false)
     */
    private $nbPlayer = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbGame", type="integer", nullable=false)
     */
    private $nbGame = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointBadge", type="integer", nullable=false)
     */
    private $pointBadge = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankBadge", type="integer", nullable=true)
     */
    private $rankBadge;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbMasterBadge", type="integer", nullable=false)
     */
    private $nbMasterBadge = 0;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", mappedBy="team")
     * @ORM\OrderBy({"pseudo" = "ASC"})
     */
    private $players;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\TeamGame", mappedBy="team")
     */
    private $teamGame;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\TeamBadge", mappedBy="team")
     */
    private $teamBadge;

    /**
     * @var Player
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLeader", referencedColumnName="id")
     * })
     */
    private $leader;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->players = new ArrayCollection();
        $this->teamGame = new ArrayCollection();
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
    public function setId(int $id)
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
     * Set libTeam
     * @param string $libTeam
     * @return Team
     */
    public function setLibTeam(string $libTeam)
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
     * @param string $tag
     * @return Team
     */
    public function setTag(string $tag)
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
     * Set leader
     * @param Player|null $leader
     * @return Team
     */
    public function setLeader(Player $leader = null)
    {
        $this->leader = $leader;

        return $this;
    }

    /**
     * Get leader
     * @return Player
     */
    public function getLeader()
    {
        return $this->leader;
    }

    /**
     * Set siteWeb
     * @param string $siteWeb
     * @return Team
     */
    public function setSiteWeb(string $siteWeb)
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
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set commentaire
     * @param string $commentaire
     * @return Team
     */
    public function setCommentaire(string $commentaire)
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
     * @param string $status
     * @return Team
     */
    public function setStatus(string $status)
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
     * @param integer $nbPlayer
     * @return Team
     */
    public function setNbPlayer(int $nbPlayer)
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
     * Set nbGame
     * @param integer $nbGame
     * @return Team
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
     * Set pointBadge
     * @param integer $pointBadge
     * @return Team
     */
    public function setPointBadge(int $pointBadge)
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
     * Set rankBadge
     * @param integer $rankBadge
     * @return Team
     */
    public function setRankBadge(int $rankBadge)
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
     * Set nbMasterBadge
     * @param integer $nbMasterBadge
     * @return Team
     */
    public function setNbMasterBadge(int $nbMasterBadge)
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
     * @return mixed
     */
    public function getTeamBadge()
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
    public static function getStatusChoices()
    {
        return [
            self::STATUS_CLOSED => self::STATUS_CLOSED,
            self::STATUS_OPENED => self::STATUS_OPENED,
        ];
    }
}
