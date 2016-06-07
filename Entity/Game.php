<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Game
 *
 * @ORM\Table(name="vgr_game", indexes={@ORM\Index(name="idxLibGameFr", columns={"libGameFr"}), @ORM\Index(name="idxLibGameEn", columns={"libGameEn"}), @ORM\Index(name="idxStatus", columns={"status"}), @ORM\Index(name="idxEtat", columns={"etat"}), @ORM\Index(name="idxSerie", columns={"idSerie"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameRepository")
 * @todo check etat / imagePlateforme / ordre
 */
class Game
{
    const NUM_ITEMS = 20;

    /**
     * @var integer
     *
     * @ORM\Column(name="idGame", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGame;

    /**
     * @var string
     *
     * @ORM\Column(name="libGameFr", type="string", length=100, nullable=true)
     */
    private $libGameFr;

    /**
     * @var string
     *
     * @ORM\Column(name="libGameEn", type="string", length=100, nullable=false)
     */
    private $libGameEn;

    /**
     * @var string
     *
     * @ORM\Column(name="picture", type="string", length=200, nullable=true)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status;

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
     * @ORM\Column(name="boolDlc", type="boolean", nullable=false)
     */
    private $boolDlc;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private $nbChart;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPost", type="integer", nullable=false)
     */
    private $nbPost;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbUser", type="integer", nullable=false)
     */
    private $nbUser;

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
     * @var Serie
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="idSerie")
     * })
     */
    private $idSerie;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", mappedBy="game")
     */
    private $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
    }

    /**
     * Get idGame
     *
     * @return integer
     */
    public function getIdGame()
    {
        return $this->idGame;
    }

    /**
     * Get libGame
     *
     * @return string
     */
    public function getLibGame()
    {
        return $this->libGameEn;
    }


    /**
     * Set libGameFr
     *
     * @param string $libGameFr
     * @return Game
     */
    public function setLibGameFr($libGameFr)
    {
        $this->libGameFr = $libGameFr;

        return $this;
    }

    /**
     * Get libGameFr
     *
     * @return string
     */
    public function getLibGameFr()
    {
        return $this->libGameFr;
    }

    /**
     * Set libGameEn
     *
     * @param string $libGameEn
     * @return Game
     */
    public function setLibGameEn($libGameEn)
    {
        $this->libGameEn = $libGameEn;

        return $this;
    }

    /**
     * Get libGameEn
     *
     * @return string
     */
    public function getLibGameEn()
    {
        return $this->libGameEn;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return Game
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return Game
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
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
     * Set nbChart
     *
     * @param integer $nbChart
     * @return Game
     */
    public function setNbChart($nbChart)
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
     * Set nbUser
     *
     * @param integer $nbUser
     * @return Game
     */
    public function setNbUser($nbUser)
    {
        $this->nbUser = $nbUser;

        return $this;
    }

    /**
     * Get nbUser
     *
     * @return integer
     */
    public function getNbUser()
    {
        return $this->nbUser;
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
     * Set idSerie
     *
     * @param Serie $idSerie
     * @return Game
     */
    public function setIdSerie(Serie $idSerie = null)
    {
        $this->idSerie = $idSerie;

        return $this;
    }

    /**
     * Get idSerie
     *
     * @return Serie
     */
    public function getIdSerie()
    {
        return $this->idSerie;
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
        $this->groups->removeElement($group);
    }

    /**
     * @return mixed
     */
    public function getGroups()
    {
        return $this->groups;
    }
}
