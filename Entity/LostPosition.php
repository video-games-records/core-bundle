<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * LostPosition
 *
 * @ORM\Table(name="vgr_lostposition", indexes={@ORM\Index(name="idxIdPlayer", columns={"idPlayer"}), @ORM\Index(name="idxIdChart", columns={"idChart"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\LostPositionRepository")
 * @ApiResource(attributes={"order"={"id": "DESC"}})
 * @ApiFilter(SearchFilter::class, properties={"player": "exact", "chart.group.game": "exact"})
 */
class LostPosition implements TimestampableInterface
{
    use TimestampableTrait;

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
     * @ORM\Column(name="oldRank", type="integer", nullable=false, options={"default":0})
     */
    private $oldRank;

    /**
     * @var integer
     *
     * @ORM\Column(name="newRank", type="integer", nullable=false, options={"default":0})
     */
    private $newRank;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="lostPositions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private $player;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", inversedBy="lostPositions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false)
     * })
     */
    private $chart;

    public function __construct()
    {
        $this->setDateCreation(new \DateTime());
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return LostPosition
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
     * Set newRank
     *
     * @param integer $newRank
     * @return LostPosition
     */
    public function setNewRank($newRank)
    {
        $this->newRank = $newRank;
        return $this;
    }

    /**
     * Get newRank
     *
     * @return integer
     */
    public function getNewRank()
    {
        return $this->newRank;
    }

    /**
     * Set oldRank
     *
     * @param integer $oldRank
     * @return LostPosition
     */
    public function setOldRank($oldRank)
    {
        $this->oldRank = $oldRank;
        return $this;
    }

    /**
     * Get oldRank
     *
     * @return integer
     */
    public function getOldRank()
    {
        return $this->oldRank;
    }

    /**
     * Set chart
     *
     * @param Chart $chart
     * @return LostPosition
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
     * Set player
     *
     * @param Player $player
     * @return LostPosition
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }
}
