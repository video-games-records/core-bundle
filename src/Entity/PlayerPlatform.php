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
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="playerPlatform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Player $player;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform", fetch="EAGER", inversedBy="playerPlatform")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlatform", referencedColumnName="id", nullable=false)
     * })
     */
    private Platform $platform;

    /**
     * @ORM\Column(name="rankPointPlatform", type="integer", nullable=false)
     */
    private int $rankPointPlatform;

    /**
     * @ORM\Column(name="pointPlatform", type="integer", nullable=false)
     */
    private int $pointPlatform = 0;

    /**
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private int $nbChart = 0;

    /**
     * Set rankPointPlatform
     * @param integer $rankPointPlatform
     * @return $this
     */
    public function setRankPointPlatform(int $rankPointPlatform): self
    {
        $this->rankPointPlatform = $rankPointPlatform;
        return $this;
    }

    /**
     * Get rankPointPlatform
     *
     * @return integer
     */
    public function getRankPointPlatform(): int
    {
        return $this->rankPointPlatform;
    }

    /**
     * Set pointPlatform
     * @param integer $pointPlatform
     * @return $this
     */
    public function setPointPlatform(int $pointPlatform): self
    {
        $this->pointPlatform = $pointPlatform;
        return $this;
    }

    /**
     * Get pointPlatform
     *
     * @return integer
     */
    public function getPointPlatform(): int
    {
        return $this->pointPlatform;
    }

    /**
     * Set nbChart
     * @param integer $nbChart
     * @return $this
     */
    public function setNbChart(int $nbChart): self
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
     * Set platform
     * @param Platform $platform
     * @return $this
     */
    public function setPlatform(Platform $platform): self
    {
        $this->platform = $platform;

        return $this;
    }


    /**
     * Get latform
     *
     * @return Platform
     */
    public function getPlatform(): Platform
    {
        return $this->platform;
    }


    /**
     * Set player
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player): self
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
