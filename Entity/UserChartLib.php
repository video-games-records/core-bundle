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
     * @ORM\Column(name="idUser", type="integer")
     * @ORM\Id
     */
    private $idUser;

    /**
     * @ORM\Column(name="idLibChart", type="integer")
     * @ORM\Id
     */
    private $idLibChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idUser", referencedColumnName="idUser")
     * })
     */
    private $user;


    /**
     * @var ChartLib
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLibChart", referencedColumnName="idLibChart")
     * })
     */
    private $lib;

    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return UserChart
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function geIdUser()
    {
        return $this->idUser;
    }


    /**
     * Get idLibChart
     *
     * @return integer
     */
    public function getIdLibChart()
    {
        return $this->idLibChart;
    }


    /**
     * Set idLibChart
     *
     * @param integer $idLibChart
     * @return UserChartLib
     */
    public function setIdLibChart($idLibChart)
    {
        $this->idLibChart = $idLibChart;
        return $this;
    }


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
     * @param ChartLib $lib
     * @return UserChartLib
     */
    public function setLib(ChartLib $lib = null)
    {
        $this->lib = $lib;
        $this->idLibChart = $lib->getIdLibChart();
        return $this;
    }

    /**
     * Get lib
     *
     * @return ChartLib
     */
    public function getLib()
    {
        return $this->lib;
    }


    /**
     * Set user
     *
     * @param User $user
     * @return UserChart
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        $this->idUser = $user->getIdUser();
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
