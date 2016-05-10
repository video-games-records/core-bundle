<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Group
 *
 * @ORM\Table(name="vgr_groupe", indexes={@ORM\Index(name="idxIdJeu", columns={"idJeu"}), @ORM\Index(name="idxLibGroupeFr", columns={"libGroupe_fr"}), @ORM\Index(name="idxLibGroupeEn", columns={"libGroupe_en"}), @ORM\Index(name="idxBoolDlc", columns={"boolDLC"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\RepositoryGroupRepository")
 */
class Group
{
    /**
     * @var string
     *
     * @ORM\Column(name="libGroupe_fr", type="string", length=100, nullable=true)
     */
    private $libGroupe_fr;

    /**
     * @var string
     *
     * @ORM\Column(name="libGroupe_en", type="string", length=100, nullable=false)
     */
    private $libGroupe_en;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolDLC", type="boolean", nullable=false)
     */
    private $boolDLC;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbRecord", type="integer", nullable=false)
     */
    private $nbRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPost", type="integer", nullable=false)
     */
    private $nbPost;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbMembre", type="integer", nullable=false)
     */
    private $nbMembre;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreation", type="datetime", nullable=false)
     */
    private $dateCreation;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModification", type="datetime", nullable=false)
     */
    private $dateModification;

    /**
     * @var integer
     *
     * @ORM\Column(name="idGroupe", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroupe;

    /**
     * @var \VideoGamesRecords\CoreBundle\Entity\Game
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="groups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idJeu", referencedColumnName="idJeu")
     * })
     */
    private $game;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\User", mappedBy="idGroupe")
     */
    private $idMembre;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idMembre = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get libGroupe
     *
     * @return string
     */
    public function getLibGroupe()
    {
        return $this->libGroupe_en;
    }

    /**
     * Set libGroupe_fr
     *
     * @param string $libGroupeFr
     * @return Group
     */
    public function setLibGroupeFr($libGroupeFr)
    {
        $this->libGroupe_fr = $libGroupeFr;

        return $this;
    }

    /**
     * Get libGroupe_fr
     *
     * @return string 
     */
    public function getLibGroupeFr()
    {
        return $this->libGroupe_fr;
    }

    /**
     * Set libGroupe_en
     *
     * @param string $libGroupeEn
     * @return Group
     */
    public function setLibGroupeEn($libGroupeEn)
    {
        $this->libGroupe_en = $libGroupeEn;

        return $this;
    }

    /**
     * Get libGroupe_en
     *
     * @return string 
     */
    public function getLibGroupeEn()
    {
        return $this->libGroupe_en;
    }

    /**
     * Set boolDLC
     *
     * @param boolean $boolDLC
     * @return Group
     */
    public function setBoolDLC($boolDLC)
    {
        $this->boolDLC = $boolDLC;

        return $this;
    }

    /**
     * Get boolDLC
     *
     * @return boolean 
     */
    public function getBoolDLC()
    {
        return $this->boolDLC;
    }

    /**
     * Set nbRecord
     *
     * @param integer $nbRecord
     * @return Group
     */
    public function setNbRecord($nbRecord)
    {
        $this->nbRecord = $nbRecord;

        return $this;
    }

    /**
     * Get nbRecord
     *
     * @return integer 
     */
    public function getNbRecord()
    {
        return $this->nbRecord;
    }

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     * @return Group
     */
    public function setNbPost($nbPost)
    {
        $this->nbPost = $nbPost;

        return $this;
    }

    /**
     * Get nbPost
     *
     * @return integer 
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * Set nbMembre
     *
     * @param integer $nbMembre
     * @return Group
     */
    public function setNbMembre($nbMembre)
    {
        $this->nbMembre = $nbMembre;

        return $this;
    }

    /**
     * Get nbMembre
     *
     * @return integer 
     */
    public function getNbMembre()
    {
        return $this->nbMembre;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Group
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation
     *
     * @return \DateTime 
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set dateModification
     *
     * @param \DateTime $dateModification
     * @return Group
     */
    public function setDateModification($dateModification)
    {
        $this->dateModification = $dateModification;

        return $this;
    }

    /**
     * Get dateModification
     *
     * @return \DateTime 
     */
    public function getDateModification()
    {
        return $this->dateModification;
    }

    /**
     * Get idGroupe
     *
     * @return integer 
     */
    public function getIdGroupe()
    {
        return $this->idGroupe;
    }

    /**
     * Set game
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Game $game
     * @return Group
     */
    public function setGame(\VideoGamesRecords\CoreBundle\Entity\Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     *
     * @return \VideoGamesRecords\CoreBundle\Entity\Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Add idMembre
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\User $idMembre
     * @return Group
     */
    public function addIdMembre(\VideoGamesRecords\CoreBundle\Entity\User $idMembre)
    {
        $this->idMembre[] = $idMembre;

        return $this;
    }

    /**
     * Remove idMembre
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\User $idMembre
     */
    public function removeIdMembre(\VideoGamesRecords\CoreBundle\Entity\User $idMembre)
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
