<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserChartLib
 *
 * @ORM\Table(name="vgr_librecord_membre", indexes={@ORM\Index(name="idxIdLibRecord", columns={"idLibRecord"}), @ORM\Index(name="idxIdMembre", columns={"idMembre"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserChartLibRepository")
 */
class UserChartLib
{

    /**
     * @ORM\Column(name="idMembre", type="integer")
     * @ORM\Id
     */
    private $idMembre;

    /**
     * @ORM\Column(name="idLibRecord", type="integer")
     * @ORM\Id
     */
    private $idLibRecord;

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
     *   @ORM\JoinColumn(name="idMembre", referencedColumnName="idMembre")
     * })
     */
    private $user;


    /**
     * @var ChartLib
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\ChartLib")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idLibRecord", referencedColumnName="idLibRecord")
     * })
     */
    private $lib;

    /**
     * Set idMembre
     *
     * @param integer $idMembre
     * @return UserChart
     */
    public function setIdMembre($idMembre)
    {
        $this->idMembre = $idMembre;
        return $this;
    }

    /**
     * Get idMembre
     *
     * @return integer
     */
    public function geIdMembre()
    {
        return $this->idMembre;
    }


    /**
     * Get idLibRecord
     *
     * @return integer
     */
    public function getIdLibRecord()
    {
        return $this->idLibRecord;
    }


    /**
     * Set idLibRecord
     *
     * @param integer $idLibRecord
     * @return UserChartLib
     */
    public function setIdLibRecord($idLibRecord)
    {
        $this->idLibRecord = $idLibRecord;
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
        $this->idLibRecord = $lib->getIdLibRecord();
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
        $this->idMembre = $user->getIdMembre();
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