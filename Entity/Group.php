<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Group
 *
 * @ORM\Table(name="vgr_group", indexes={@ORM\Index(name="idxIdGame", columns={"idGame"}), @ORM\Index(name="idxLibGroupFr", columns={"libGroupFr"}), @ORM\Index(name="idxLibGroupEn", columns={"libGroupEn"}), @ORM\Index(name="idxBoolDlc", columns={"boolDlc"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GroupRepository")
 */
class Group
{
    use Timestampable;

    /**
     * @var integer
     * @ORM\Column(name="idGroup", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idGroup;

    /**
     * @var integer
     * @ORM\Column(name="idGame", type="integer", nullable=false)
     */
    private $idGame;


    /**
     * @var string
     * @ORM\Column(name="libGroupFr", type="string", length=100, nullable=true)
     */
    private $libGroupFr;

    /**
     * @var string
     * @ORM\Column(name="libGroupEn", type="string", length=100, nullable=false)
     */
    private $libGroupEn;

    /**
     * @var boolean
     * @ORM\Column(name="boolDlc", type="boolean", nullable=false)
     */
    private $boolDlc = 0;

    /**
     * @var integer
     * @ORM\Column(name="nbChart", type="integer", nullable=false)
     */
    private $nbChart = 0;

    /**
     * @var integer
     * @ORM\Column(name="nbPost", type="integer", nullable=false)
     */
    private $nbPost = 0;

    /**
     * @var integer
     * @ORM\Column(name="nbUser", type="integer", nullable=false)
     */
    private $nbUser = 0;

    /**
     * @var Game
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="groups")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="idGame")
     * })
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart", mappedBy="group")
     */
    private $charts;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->charts = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString() {
        return sprintf('%s [%s]', $this->libGroupEn, $this->idGroup);
    }

    /**
     * Set idGroup
     * @param integer $idGroup
     * @return Group
     */
    public function setIdGroup($idGroup)
    {
        $this->idGroup = $idGroup;
        return $this;
    }

    /**
     * Get idGroup
     * @return integer
     */
    public function getIdGroup()
    {
        return $this->idGroup;
    }


    /**
     * Get libGroup
     * @return string
     */
    public function getLibGroup()
    {
        return $this->libGroupEn;
    }

    /**
     * Set libGroupFr
     * @param string $libGroupFr
     * @return Group
     */
    public function setLibGroupFr($libGroupFr)
    {
        $this->libGroupFr = $libGroupFr;

        return $this;
    }

    /**
     * Get libGroupFr
     * @return string
     */
    public function getLibGroupFr()
    {
        return $this->libGroupFr;
    }

    /**
     * Set libGroupEn
     * @param string $libGroupEn
     * @return Group
     */
    public function setLibGroupEn($libGroupEn)
    {
        $this->libGroupEn = $libGroupEn;

        return $this;
    }

    /**
     * Get libGroupEn
     * @return string
     */
    public function getLibGroupEn()
    {
        return $this->libGroupEn;
    }

    /**
     * Set boolDlc
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
     * @return boolean
     */
    public function getBoolDlc()
    {
        return $this->boolDlc;
    }

    /**
     * Set nbChart
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
     * @return integer
     */
    public function getNbChart()
    {
        return $this->nbChart;
    }

    /**
     * Set nbPost
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
     * @return integer
     */
    public function getNbPost()
    {
        return $this->nbPost;
    }

    /**
     * Set nbUser
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
     * @return integer
     */
    public function getNbUser()
    {
        return $this->nbUser;
    }

    /**
     * Set game
     * @param Game $game
     * @return Group
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;
        $this->setIdGame($game->getIdGame());
        return $this;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @param Chart $chart
     * @return $this
     */
    public function addChart(Chart $chart)
    {
        $this->charts[] = $chart;
        return $this;
    }

    /**
     * @param Chart $chart
     */
    public function removeChart(Chart $chart)
    {
        $this->charts->removeElement($chart);
    }

    /**
     * @return mixed
     */
    public function getCharts()
    {
        return $this->charts;
    }

    /**
     * Set idGame
     *
     * @param integer $idGame
     * @return Group
     */
    public function setIdGame($idGame)
    {
        $this->idGame = $idGame;

        return $this;
    }

    /**
     * Get idGame
     * @return integer
     */
    public function getIdGame()
    {
        return $this->idGame;
    }
}
