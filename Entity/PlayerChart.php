<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Exception;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Eko\FeedBundle\Item\Writer\ItemInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;

/**
 * PlayerChart
 *
 * @ORM\Table(name="vgr_player_chart")
 * @DoctrineAssert\UniqueEntity(fields={"chart", "player"}, message="A score already exists for the couple player / chart")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\PlayerChartListener"})
 * @ApiFilter(DateFilter::class, properties={"lastUpdate": DateFilter::EXCLUDE_NULL})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "status": "exact",
 *          "player": "exact",
 *          "chart": "exact",
 *          "chart.group": "exact",
 *          "chart.group.game": "exact",
 *      }
 * )
 * @ApiFilter(
 *     GroupFilter::class,
 *     arguments={
 *          "parameterName": "groups",
 *          "overrideDefaultGroups": true,
 *          "whitelist": {
 *              "playerChart.read",
 *              "playerChart.status",
 *              "playerChartStatus.read",
 *              "playerChart.player",
 *              "player.read.mini",
 *              "chart.read.mini",
 *              "playerChart.chart",
 *              "chart.group",
 *              "group.read.mini",
 *              "group.game",
 *              "game.read.mini",
 *              "playerChartLib.format",
 *              "playerChart.proof",
 *              "proof.read",
 *              "picture.read",
 *              "video.read",
 *          }
 *     }
 * )
 * @ApiFilter(
 *     OrderFilter::class,
 *     properties={
 *          "id":"ASC",
 *          "lastUpdate" : "DESC",
 *          "rank" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class PlayerChart implements ItemInterface, TimestampableInterface
{
    use TimestampableTrait;
    use \VideoGamesRecords\CoreBundle\Model\Player;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var integer
     *
     * @ORM\Column(name="`rank`", type="integer", nullable=true)
     */
    private $rank;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbEqual", type="integer", nullable=false)
     */
    private $nbEqual = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChart", type="integer", nullable=false)
     */
    private $pointChart = 0;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isTopScore", type="boolean", nullable=false)
     */
    private $topScore = false;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="lastUpdate", type="datetime", nullable=false)
     */
    private $lastUpdate;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="dateInvestigation", type="date", nullable=true)
     */
    private $dateInvestigation;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", inversedBy="playerCharts", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false)
     * })
     */
    private $chart;

    /**
     * @var Proof
     *
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Proof", inversedBy="playerChart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProof", referencedColumnName="id")
     * })
     */
    private $proof;

    /**
     * @var PlayerChartStatus
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatus", referencedColumnName="id", nullable=false)
     * })
     */
    private $status;

    /**
     * @var Platform
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlatform", referencedColumnName="id")
     * })
     */
    private $platform;

    /**
     * @ORM\OneToMany(
     *     targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChartLib",
     *     mappedBy="playerChart",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private $libs;

    private $link;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->libs = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s # %s [%s]', $this->getChart()->getDefaultName(), $this->getPlayer()->getPseudo(), $this->id);
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return PlayerChart
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
     * Set rank
     *
     * @param integer $rank
     * @return PlayerChart
     */
    public function setRank(int $rank)
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * Set nbEqual
     *
     * @param integer $nbEqual
     * @return PlayerChart
     */
    public function setNbEqual(int $nbEqual)
    {
        $this->nbEqual = $nbEqual;
        return $this;
    }

    /**
     * Get nbEqual
     *
     * @return integer
     */
    public function getNbEqual()
    {
        return $this->nbEqual;
    }

    /**
     * Set pointChart
     * @param float $pointChart
     * @return PlayerChart
     */
    public function setPointChart(float $pointChart)
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return float
     */
    public function getPointChart()
    {
        return $this->pointChart;
    }

    /**
     * Set topScore
     *
     * @param bool $topScore
     *
     * @return PlayerChart
     */
    public function setTopScore(bool $topScore)
    {
        $this->topScore = $topScore;
        return $this;
    }

    /**
     * Get topScore
     *
     * @return bool
     */
    public function isTopScore()
    {
        return $this->topScore;
    }

    /**
     * Set lastUpdate
     *
     * @param DateTime $lastUpdate
     * @return $this
     */
    public function setLastUpdate(DateTime $lastUpdate)
    {
        $this->lastUpdate = $lastUpdate;

        return $this;
    }

    /**
     * Get lastUpdate
     *
     * @return DateTime
     */
    public function getLastUpdate()
    {
        return $this->lastUpdate;
    }

    /**
     * Set dateInvestigation
     *
     * @param DateTime $dateInvestigation
     * @return PlayerChart
     */
    public function setDateInvestigation(DateTime $dateInvestigation)
    {
        $this->dateInvestigation = $dateInvestigation;
        return $this;
    }

    /**
     * Get dateInvestigation
     *
     * @return DateTime
     */
    public function getDateInvestigation()
    {
        return $this->dateInvestigation;
    }

    /**
     * Set Chart
     * @param Chart|null $chart
     * @return $this
     */
    public function setChart(Chart $chart = null)
    {
        $this->chart = $chart;

        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart()
    {
        return $this->chart;
    }

    /**
     * Set proof
     * @param Proof|null $proof
     * @return $this
     */
    public function setProof(Proof $proof = null)
    {
        $this->proof = $proof;

        return $this;
    }

    /**
     * Get proof
     *
     * @return Proof
     */
    public function getProof()
    {
        return $this->proof;
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
     * Get platform
     *
     * @return Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }


    /**
     * Set status
     * @param PlayerChartStatus|object|null $status
     * @return $this
     */
    public function setStatus(PlayerChartStatus $status = null)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return PlayerChartStatus
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param PlayerChartLib $lib
     * @return $this
     */
    public function addLib(PlayerChartLib $lib)
    {
        $lib->setPlayerChart($this);
        $this->libs[] = $lib;
        return $this;
    }

    /**
     * @param PlayerChartLib $lib
     */
    public function removeLib(PlayerChartLib $lib)
    {
        $this->libs->removeElement($lib);
    }

    /**
     * @return ArrayCollection|PlayerChartLib[]
     */
    public function getLibs()
    {
        return $this->libs;
    }

    /**
     * Set link
     *
     * @param string $link
     * @return string
     */
    public function setLink(string $link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * Get link
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }

    /**
     * @return string
     */
    public function getFeedItemTitle()
    {
        return sprintf(
            'New score on %s by %s rank#%d',
            $this->getChart()->getGroup()->getGame()->getName(),
            $this->getPlayer()->getPseudo(),
            $this->getRank()
        );
    }

    /**
     * @return string
     */
    public function getFeedItemDescription()
    {
        return null;
    }

    /**
     * @return DateTime
     */
    public function getFeedItemPubDate()
    {
        return $this->getLastUpdate();
    }

    /**
     * @return string
     */
    public function getFeedItemLink()
    {
        return $this->getLink();
    }


    /**
     * @return string
     */
    public function getUrl()
    {
        return sprintf(
            '%s-game-g%d/%s-group-g%d/%s-chart-c%d/pc-%d/index',
            $this->getChart()->getGroup()->getGame()->getSlug(),
            $this->getChart()->getGroup()->getGame()->getId(),
            $this->getChart()->getGroup()->getSlug(),
            $this->getChart()->getGroup()->getId(),
            $this->getChart()->getSlug(),
            $this->getChart()->getId(),
            $this->getId()
        );
    }
}
