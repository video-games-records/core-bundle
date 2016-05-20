<?php

namespace VideoGamesRecords\CoreBundle\Entity;

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
     * Constructor
     */
    public function __construct()
    {

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

}
