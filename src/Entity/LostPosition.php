<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * LostPosition
 *
 * @ORM\Table(name="vgr_lostposition")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\LostPositionRepository")
 * @ApiResource(attributes={"order"={"id": "DESC"}, "pagination_items_per_page"=20})
 * @ApiFilter(SearchFilter::class, properties={"player": "exact", "chart.group.game": "exact"})
 */
class LostPosition
{
    use TimestampableEntity;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="oldRank", type="integer", nullable=false, options={"default":0})
     */
    private int $oldRank = 0;

    /**
     * @ORM\Column(name="newRank", type="integer", nullable=false, options={"default":0})
     */
    private int $newRank = 0;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Player $player;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Chart $chart;

    public function __construct()
    {
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return LostPosition
     */
    public function setId(int $id): Self
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
     * Set newRank
     *
     * @param integer $newRank
     * @return LostPosition
     */
    public function setNewRank(int $newRank): Self
    {
        $this->newRank = $newRank;
        return $this;
    }

    /**
     * Get newRank
     *
     * @return integer
     */
    public function getNewRank(): int
    {
        return $this->newRank;
    }

    /**
     * Set oldRank
     *
     * @param integer $oldRank
     * @return LostPosition
     */
    public function setOldRank(int $oldRank): Self
    {
        $this->oldRank = $oldRank;
        return $this;
    }

    /**
     * Get oldRank
     *
     * @return integer
     */
    public function getOldRank(): int
    {
        return $this->oldRank;
    }

    /**
     * Set chart
     * @param Chart $chart
     * @return LostPosition
     */
    public function setChart(Chart $chart): Self
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
     * Set player
     * @param Player $player
     * @return LostPosition
     */
    public function setPlayer(Player $player): Self
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }
}
