<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Serie
 *
 * @ORM\Table(name="vgr_serie")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\SerieRepository")
 */
class Serie
{
    /**
     * @var string
     *
     * @ORM\Column(name="libSerie", type="string", length=100, nullable=false)
     */
    private $libSerie;

    /**
     * @var integer
     *
     * @ORM\Column(name="idSerie", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idSerie;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\User", mappedBy="idSerie")
     */
    private $idMembre;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idMembre = new ArrayCollection();
    }


    /**
     * Set libSerie
     *
     * @param string $libSerie
     * @return Serie
     */
    public function setLibSerie($libSerie)
    {
        $this->libSerie = $libSerie;

        return $this;
    }

    /**
     * Get libSerie
     *
     * @return string 
     */
    public function getLibSerie()
    {
        return $this->libSerie;
    }

    /**
     * Get idSerie
     *
     * @return integer 
     */
    public function getIdSerie()
    {
        return $this->idSerie;
    }

    /**
     * Add idMembre
     *
     * @param User $idMembre
     * @return Serie
     */
    public function addIdMembre(User $idMembre)
    {
        $this->idMembre[] = $idMembre;

        return $this;
    }

    /**
     * Remove idMembre
     *
     * @param User $idMembre
     */
    public function removeIdMembre(User $idMembre)
    {
        $this->idMembre->removeElement($idMembre);
    }

    /**
     * Get idMembre
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getIdMembre()
    {
        return $this->idMembre;
    }
}
