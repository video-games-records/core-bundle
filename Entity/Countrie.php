<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Countrie
 *
 * @ORM\Table(name="t_pays", indexes={@ORM\Index(name="idxLibPaysFr", columns={"libPays_fr"}), @ORM\Index(name="idxLibPaysEn", columns={"libPays_en"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\CountrieRepository")
 */
class Countrie
{
    /**
     * @var string
     *
     * @ORM\Column(name="libPays_fr", type="string", length=100, nullable=false)
     */
    private $libPays_fr;

    /**
     * @var string
     *
     * @ORM\Column(name="libPays_en", type="string", length=100, nullable=false)
     */
    private $libPays_en;

    /**
     * @var string
     *
     * @ORM\Column(name="codeIso", type="string", length=30, nullable=false)
     */
    private $codeIso;

    /**
     * @var integer
     *
     * @ORM\Column(name="idPays", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPays;



    /**
     * Set libPays_fr
     *
     * @param string $libPaysFr
     * @return Countrie
     */
    public function setLibPaysFr($libPaysFr)
    {
        $this->libPays_fr = $libPaysFr;

        return $this;
    }

    /**
     * Get libPays_fr
     *
     * @return string 
     */
    public function getLibPaysFr()
    {
        return $this->libPays_fr;
    }

    /**
     * Set libPays_en
     *
     * @param string $libPaysEn
     * @return Countrie
     */
    public function setLibPaysEn($libPaysEn)
    {
        $this->libPays_en = $libPaysEn;

        return $this;
    }

    /**
     * Get libPays_en
     *
     * @return string 
     */
    public function getLibPaysEn()
    {
        return $this->libPays_en;
    }

    /**
     * Set codeIso
     *
     * @param string $codeIso
     * @return Countrie
     */
    public function setCodeIso($codeIso)
    {
        $this->codeIso = $codeIso;

        return $this;
    }

    /**
     * Get codeIso
     *
     * @return string 
     */
    public function getCodeIso()
    {
        return $this->codeIso;
    }

    /**
     * Get idPays
     *
     * @return integer 
     */
    public function getIdPays()
    {
        return $this->idPays;
    }
}
