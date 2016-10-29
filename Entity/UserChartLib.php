<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserChartLib
 *
 * @ORM\Table(name="vgr_user_chartlib", indexes={@ORM\Index(name="idxIdLibChart", columns={"idLibChart"}), @ORM\Index(name="idxIdUser", columns={"idUser"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserChartLibRepository")
 */
class UserChartLib
{
    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="idUser")
     * })
     * @ORM\Id
     */
    private $user;


    /**
     * @var ChartLib
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLibChart", referencedColumnName="idLibChart")
     * })
     * @ORM\Id
     */
    private $libChart;

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }


    /**
     * Set value
     *
     * @param integer $value
     * @return UserChartLib
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }


    /**
     * Set lib
     *
     * @param ChartLib $libChart
     * @return UserChartLib
     */
    public function setLibChart(ChartLib $libChart = null)
    {
        $this->libChart = $libChart;
        return $this;
    }

    /**
     * Get lib
     *
     * @return ChartLib
     */
    public function getLibChart()
    {
        return $this->libChart;
    }


    /**
     * Set user
     *
     * @param Player $user
     * @return UserChart
     */
    public function setUser(Player $user = null)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return Player
     */
    public function getUser()
    {
        return $this->user;
    }
}
