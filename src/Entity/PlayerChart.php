<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\RangeFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\ExistsFilter;
use ApiPlatform\Core\Serializer\Filter\GroupFilter;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use VideoGamesRecords\CoreBundle\Traits\Entity\LastUpdateTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbEqualTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerTrait;

/**
 * PlayerChart
 *
 * @ORM\Table(
 *     name="vgr_player_chart",
 *     uniqueConstraints={
 *         @ORM\UniqueConstraint(name="unq_player_chart", columns={"idPlayer", "idChart"})
 *     },
 *     indexes={
 *         @ORM\Index(name="idx_rank", columns={"`rank`"}),
 *         @ORM\Index(name="idx_point_chart", columns={"pointChart"}),
 *         @ORM\Index(name="idx_top_score", columns={"isTopScore"}),
 *         @ORM\Index(name="idx_last_update_player", columns={"lastUpdate", "idPlayer"})
 *     }
 * )
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
 *          "chart.group.game.platforms": "exact",
 *          "rank": "exact",
 *          "nbEqual": "exact",
 *          "chart.libChartEn" : "partial",
 *          "chart.libChartFr" : "partial",
 *     }
 * )
 * @ApiFilter(
 *     RangeFilter::class,
 *     properties={
 *         "chart.nbPost",
 *         "rank",
 *         "pointChart",
 *     }
 * )
 * @ApiFilter(
 *     ExistsFilter::class,
 *     properties={
 *         "proof",
 *         "proof.picture",
 *         "proof.video",
 *     }
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
 *              "playerChart.platform",
 *              "platform.read",
 *              "playerChart.player",
 *              "player.read.mini",
 *              "player.country",
 *              "country.read",
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
 *          "pointChart" : "DESC",
 *          "chart.libChartEn" : "ASC",
 *          "chart.libChartFr" : "ASC",
 *          "chart.group.libGroupEn" : "ASC",
 *          "chart.group.libGroupFr" : "ASC",
 *          "chart.group.game.libGameEn" : "ASC",
 *          "chart.group.game.libGameFr" : "ASC",
 *     },
 *     arguments={"orderParameterName"="order"}
 * )
 */
class PlayerChart
{
    use PlayerTrait;
    use TimestampableEntity;
    use NbEqualTrait;
    use LastUpdateTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="`rank`", type="integer", nullable=true)
     */
    private ?int $rank = null;

    /**
     * @ORM\Column(name="pointChart", type="integer", nullable=false, options={"default" : 0})
     */
    private int $pointChart = 0;

    /**
     * @ORM\Column(name="pointPlatform", type="integer", nullable=false, options={"default" : 0})
     */
    private int $pointPlatform = 0;

    /**
     * @ORM\Column(name="isTopScore", type="boolean", nullable=false)
     */
    private bool $topScore = false;

    /**
     * @ORM\Column(name="dateInvestigation", type="date", nullable=true)
     */
    private ?DateTime $dateInvestigation = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", inversedBy="playerCharts", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Chart $chart;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Proof", inversedBy="playerChart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idProof", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     * })
     */
    private ?Proof $proof = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChartStatus", inversedBy="playerCharts")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idStatus", referencedColumnName="id", nullable=false)
     * })
     */
    private PlayerChartStatus $status;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlatform", referencedColumnName="id")
     * })
     */
    private ?Platform $platform = null;

    /**
     * @var Collection<PlayerChartLib>
     * @ORM\OneToMany(
     *     targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChartLib",
     *     mappedBy="playerChart",
     *     cascade={"persist", "remove"},
     *     orphanRemoval=true
     * )
     */
    private Collection $libs;


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
    public function setId(int $id): PlayerChart
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
     * Set rank
     *
     * @param integer $rank
     * @return PlayerChart
     */
    public function setRank(int $rank): PlayerChart
    {
        $this->rank = $rank;
        return $this;
    }

    /**
     * Get rank
     *
     * @return integer
     */
    public function getRank(): ?int
    {
        return $this->rank;
    }


    /**
     * Set pointChart
     * @param int $pointChart
     * @return PlayerChart
     */
    public function setPointChart(int $pointChart): PlayerChart
    {
        $this->pointChart = $pointChart;
        return $this;
    }

    /**
     * Get pointChart
     *
     * @return int
     */
    public function getPointChart(): int
    {
        return $this->pointChart;
    }

    /**
     * Set pointPlatform
     * @param int $pointPlatform
     * @return PlayerChart
     */
    public function setPointPlatform(int $pointPlatform): PlayerChart
    {
        $this->pointPlatform = $pointPlatform;
        return $this;
    }

    /**
     * Get pointPlatform
     *
     * @return int
     */
    public function getPointPlatform(): ?int
    {
        return $this->pointPlatform;
    }

    /**
     * Set topScore
     *
     * @param bool $topScore
     *
     * @return PlayerChart
     */
    public function setTopScore(bool $topScore): PlayerChart
    {
        $this->topScore = $topScore;
        return $this;
    }

    /**
     * Get topScore
     *
     * @return bool
     */
    public function isTopScore(): bool
    {
        return $this->topScore;
    }



    /**
     * Set dateInvestigation
     * @param DateTime|null $dateInvestigation
     * @return PlayerChart
     */
    public function setDateInvestigation(DateTime $dateInvestigation = null): PlayerChart
    {
        $this->dateInvestigation = $dateInvestigation;
        return $this;
    }

    /**
     * Get dateInvestigation
     *
     * @return DateTime
     */
    public function getDateInvestigation(): ?DateTime
    {
        return $this->dateInvestigation;
    }

    /**
     * Set Chart
     * @param Chart $chart
     * @return PlayerChart
     */
    public function setChart(Chart $chart): PlayerChart
    {
        $this->chart = $chart;

        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart(): Chart
    {
        return $this->chart;
    }

    /**
     * Set proof
     * @param Proof|null $proof
     * @return PlayerChart
     */
    public function setProof(Proof $proof = null): PlayerChart
    {
        $this->proof = $proof;

        return $this;
    }

    /**
     * Get proof
     *
     * @return Proof
     */
    public function getProof(): ?Proof
    {
        return $this->proof;
    }


    /**
     * Set platform
     * @param Platform|null $platform
     * @return PlayerChart
     */
    public function setPlatform(Platform $platform = null): PlayerChart
    {
        $this->platform = $platform;
        return $this;
    }

    /**
     * Get platform
     *
     * @return Platform
     */
    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }


    /**
     * Set status
     * @param PlayerChartStatus $status
     * @return PlayerChart
     */
    public function setStatus(PlayerChartStatus $status): PlayerChart
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return PlayerChartStatus
     */
    public function getStatus(): PlayerChartStatus
    {
        return $this->status;
    }

    /**
     * @param PlayerChartLib $lib
     * @return PlayerChart
     */
    public function addLib(PlayerChartLib $lib): PlayerChart
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
     * @return Collection
     */
    public function getLibs(): Collection
    {
        return $this->libs;
    }

    /**
     * @return string
     */
    public function getValuesAsString() : String
    {
        $values = [];
        foreach ($this->getLibs() as $lib) {
            $values[] = $lib->getValue();
        }
        return implode('|', $values);
    }

    /**
     * @return string
     */
    public function getUrl(): string
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
