<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PlayerPlatform
 *
 * @ORM\Table(name="vgr_player_platform")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerPlatformRepository")
 */
class PlayerPlatform
{
    /**
     * @var Player
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="playerPlatform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private $player;

    /**
     * @var Platform
     *
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform", fetch="EAGER", inversedBy="playerPlatform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlatform", referencedColumnName="id", nullable=false)
     * })
     */
    private $platform;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankPointPlatform", type="integer", nullable=false)
     */
    private $rankPointPlatform;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointPlatform", type="integer", nullable=false)
     */
    private $pointPlatform = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private $nbChart = 0;

    /**
     * Set rankPointPlatform
     * @param integer $rankPointPlatform
     * @return $this
     */
    public function setRankPointPlatform(int $rankPointPlatform)
    {
        $this->rankPointPlatform = $rankPointPlatform;
        return $this;
    }

    /**
     * Get rankPointPlatform
     *
     * @return integer
     */
    public function getRankPointPlatform()
    {
        return $this->rankPointPlatform;
    }

    /**
     * Set pointPlatform
     * @param integer $pointPlatform
     * @return $this
     */
    public function setPointPlatform(int $pointPlatform)
    {
        $this->pointPlatform = $pointPlatform;
        return $this;
    }

    /**
     * Get pointPlatform
     *
     * @return integer
     */
    public function getPointPlatform()
    {
        return $this->pointPlatform;
    }

    /**
     * Set nbChart
     * @param integer $nbChart
     * @return $this
     */
    public function setNbChart(int $nbChart)
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
     * Get latform
     *
     * @return Platform
     */
    public function getPlatform()
    {
        return $this->platform;
    }


    /**
     * Set player
     * @param Player|null $player
     * @return $this
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
