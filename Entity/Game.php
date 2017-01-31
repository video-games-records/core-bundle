<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="vgr_game", indexes={@ORM\Index(name="idxLibGameFr", columns={"libGameFr"}), @ORM\Index(name="idxLibGameEn", columns={"libGameEn"}), @ORM\Index(name="idxStatus", columns={"status"}), @ORM\Index(name="idxEtat", columns={"etat"}), @ORM\Index(name="idxSerie", columns={"idSerie"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameRepository")
 * @todo check etat / imagePlateforme / ordre
 */
class Game
{
    use Timestampable;

    const NUM_ITEMS = 20;

    const STATUS_ACTIVE = 'ACTIF';
    const STATUS_INACTIVE = 'INACTIF';

    const ETAT_INIT = 'CREATION';
    const ETAT_CHART = 'RECORD';
    const ETAT_PICTURE = 'IMAGE';
    const ETAT_END = 'FINI';


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
     * @Assert\Length(max="100")
     * @ORM\Column(name="libGameFr", type="string", length=100, nullable=true)
     */
    private $libGameFr;

    /**
     * @var string
     *
     * @Assert\Length(max="100")
     * @ORM\Column(name="libGameEn", type="string", length=100, nullable=false)
     */
    private $libGameEn;

    /**
     * @var string
     *
     * @Assert\Length(max="200")
     * @ORM\Column(name="picture", type="string", length=200, nullable=true)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = 'INACTIF';

    /**
     * @var string
     *
     * @ORM\Column(name="etat", type="string", nullable=false)
     */
    private $etat = 'CREATION';

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateActivation", type="datetime", nullable=true)
     */
    private $dateActivation;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolDlc", type="boolean", nullable=false, options={"default":0})
     */
    private $boolDlc = false;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbChart", type="integer", nullable=false, options={"default":0})
     */
    private $nbChart = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPost", type="integer", nullable=false, options={"default":0})
     */
    private $nbPost = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbUser", type="integer", nullable=false, options={"default":0})
     */
    private $nbUser = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="ordre", type="integer", nullable=true)
     */
    private $ordre;

    /**
     * @var integer
     *
     * @ORM\Column(name="idSerie", type="integer", nullable=true)
     */
    private $idSerie;

    /**
     * @var Serie
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="idSerie")
     * })
     */
    private $serie;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group", mappedBy="game", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $groups;

    /**
     * @ORM\ManyToMany(targetEntity="Platform")
     * @ORM\JoinTable(name="vgr_game_platform",
     *      joinColumns={@ORM\JoinColumn(name="idGame", referencedColumnName="idGame")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idPlatform", referencedColumnName="idPlatform")}
     *      )
     * @ORM\OrderBy({"libPlatform" = "ASC"})
     */
    private $platforms;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new ArrayCollection();
        $this->platforms = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString() {
        return sprintf('%s [%s]', $this->libGameEn, $this->idGame);
    }

    /**
     * Set idGame
     *
     * @param integer $idGame
     * @return Game
     */
    public function setIdGame($idGame)
    {
        $this->idGame = $idGame;
        return $this;
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
     * Set idSerie
     *
     * @param integer $idSerie
     * @return Game
     */
    public function setIdSerie($idSerie)
    {
        $this->idSerie = $idSerie;

        return $this;
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
     * Set serie
     *
     * @param Serie $serie
     * @return Game
     */
    public function setSerie(Serie $serie = null)
    {
        $this->serie = $serie;
        $this->setIdSerie($serie->getIdSerie());
        return $this;
    }

    /**
     * Get idSerie
     *
     * @return Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }

    /**
     * @param Group $group
     * @return $this
     */
    public function addGroup(Group $group)
    {
        $group->setGame($this);
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


    /**
     * @param Platform $platform
     * @return $this
     */
    public function addPlatform(Platform $platform)
    {
        $this->platforms[] = $platform;
        return $this;
    }

    /**
     * @param Platform $platform
     */
    public function removePlatform(Platform $platform)
    {
        $this->groups->removeElement($platform);
    }

    /**
     * @return mixed
     */
    public function getPlatforms()
    {
        return $this->platforms;
    }

    /**
     * @return array
     */
    public static function getStatusChoices() {
        return array(
            self::STATUS_ACTIVE => self::STATUS_ACTIVE,
            self::STATUS_INACTIVE => self::STATUS_INACTIVE,
        );
    }

    /**
     * @return array
     */
    public static function getEtatsChoices() {
        return array(
            self::ETAT_INIT => self::ETAT_INIT,
            self::ETAT_CHART => self::ETAT_CHART,
            self::ETAT_PICTURE => self::ETAT_PICTURE,
            self::ETAT_END => self::ETAT_END,
        );
    }
}
