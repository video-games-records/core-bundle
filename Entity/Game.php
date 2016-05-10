<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Game
 *
 * @ORM\Table(name="vgr_jeu", indexes={@ORM\Index(name="idxLibJeuFr", columns={"libJeu_fr"}), @ORM\Index(name="idxLibJeuEn", columns={"libJeu_en"}), @ORM\Index(name="idxStatut", columns={"statut"}), @ORM\Index(name="idxEtat", columns={"etat"}), @ORM\Index(name="idxSerie", columns={"idSerie"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameRepository")
 */
class Game
{
    const NUM_ITEMS = 20;

    /**
     * @var string
     *
     * @ORM\Column(name="libJeu_fr", type="string", length=100, nullable=true)
     */
    private $libJeu_fr;

    /**
     * @var string
     *
     * @ORM\Column(name="libJeu_en", type="string", length=100, nullable=false)
     */
    private $libJeu_en;

    /**
     * @var string
     *
     * @ORM\Column(name="imageJeu", type="string", length=200, nullable=true)
     */
    private $imageJeu;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", nullable=false)
     */
    private $statut;

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", nullable=false)
     */
    private $etat;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateActivation", type="datetime", nullable=true)
     */
    private $dateActivation;

    /**
     * @var string
     *
     * @ORM\Column(name="imagePlateForme", type="text", length=65535, nullable=false)
     */
    private $imagePlateforme;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolDLC", type="boolean", nullable=false)
     */
    private $boolDlc;

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
     * @var integer
     *
     * @ORM\Column(name="ordre", type="integer", nullable=true)
     */
    private $ordre;

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
     * @ORM\Column(name="idJeu", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idJeu;

    /**
     * @var \VideoGamesRecords\CoreBundle\Serie
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="idSerie")
     * })
     */
    private $idSerie;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\User", mappedBy="idJeu")
     */
    private $idMembre;


    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", mappedBy="game")
     */
    private $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->idMembre = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Get libJeu
     *
     * @return string
     */
    public function getLibJeu()
    {
        return $this->libJeu_en;
    }


    /**
     * Set libJeu_fr
     *
     * @param string $libJeuFr
     * @return Game
     */
    public function setLibJeuFr($libJeuFr)
    {
        $this->libJeu_fr = $libJeuFr;

        return $this;
    }

    /**
     * Get libJeu_fr
     *
     * @return string 
     */
    public function getLibJeuFr()
    {
        return $this->libJeu_fr;
    }

    /**
     * Set libJeu_en
     *
     * @param string $libJeuEn
     * @return Game
     */
    public function setLibJeuEn($libJeuEn)
    {
        $this->libJeu_en = $libJeuEn;

        return $this;
    }

    /**
     * Get libJeu_en
     *
     * @return string 
     */
    public function getLibJeuEn()
    {
        return $this->libJeu_en;
    }

    /**
     * Set imageJeu
     *
     * @param string $imageJeu
     * @return Game
     */
    public function setImageJeu($imageJeu)
    {
        $this->imageJeu = $imageJeu;

        return $this;
    }

    /**
     * Get imageJeu
     *
     * @return string 
     */
    public function getImageJeu()
    {
        return $this->imageJeu;
    }

    /**
     * Set statut
     *
     * @param string $statut
     * @return Game
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut
     *
     * @return string 
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set etat
     *
     * @param string $etat
     * @return Game
     */
    public function setEtat($etat)
    {
        $this->etat = $etat;

        return $this;
    }

    /**
     * Get etat
     *
     * @return string 
     */
    public function getEtat()
    {
        return $this->etat;
    }

    /**
     * Set dateActivation
     *
     * @param \DateTime $dateActivation
     * @return Game
     */
    public function setDateActivation($dateActivation)
    {
        $this->dateActivation = $dateActivation;

        return $this;
    }

    /**
     * Get dateActivation
     *
     * @return \DateTime 
     */
    public function getDateActivation()
    {
        return $this->dateActivation;
    }

    /**
     * Set imagePlateforme
     *
     * @param string $imagePlateforme
     * @return Game
     */
    public function setImagePlateforme($imagePlateforme)
    {
        $this->imagePlateforme = $imagePlateforme;

        return $this;
    }

    /**
     * Get imagePlateforme
     *
     * @return string 
     */
    public function getImagePlateforme()
    {
        return $this->imagePlateforme;
    }

    /**
     * Set boolDlc
     *
     * @param boolean $boolDlc
     * @return Game
     */
    public function setBoolDlc($boolDlc)
    {
        $this->boolDlc = $boolDlc;

        return $this;
    }

    /**
     * Get boolDlc
     *
     * @return boolean 
     */
    public function getBoolDlc()
    {
        return $this->boolDlc;
    }

    /**
     * Set nbRecord
     *
     * @param integer $nbRecord
     * @return Game
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
     * @return Game
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
     * @return Game
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
     * Set ordre
     *
     * @param integer $ordre
     * @return Game
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;

        return $this;
    }

    /**
     * Get ordre
     *
     * @return integer 
     */
    public function getOrdre()
    {
        return $this->ordre;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Game
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
     * @return Game
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
     * Get idJeu
     *
     * @return integer 
     */
    public function getIdJeu()
    {
        return $this->idJeu;
    }

    /**
     * Set idSerie
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Serie $idSerie
     * @return Game
     */
    public function setIdSerie(\VideoGamesRecords\CoreBundle\Entity\Serie $idSerie = null)
    {
        $this->idSerie = $idSerie;

        return $this;
    }

    /**
     * Get idSerie
     *
     * @return \VideoGamesRecords\CoreBundle\Entity\Serie
     */
    public function getIdSerie()
    {
        return $this->idSerie;
    }

    /**
     * Add idMembre
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\User $idMembre
     * @return Game
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

    /**
     * @param Group $group
     * @return $this
     */
    public function addGroup(Group $group)
    {
        $this->groups[] = $group;
        return $this;
    }

    /**
     * @param Group $group
     */
    public function removeGroup(Group $group)
    {
        $this->groups->removeGroup($group);
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
