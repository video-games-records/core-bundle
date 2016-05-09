<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_record", indexes={@ORM\Index(name="idGroupe", columns={"idGroupe"}), @ORM\Index(name="idxStatut", columns={"statut"}), @ORM\Index(name="idxStatutTeam", columns={"statutTeam"}), @ORM\Index(name="idxLibRecordFr", columns={"libRecord_fr"}), @ORM\Index(name="idxLibRecordEn", columns={"libRecord_en"}), @ORM\Index(name="idRecord", columns={"idRecord"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartRepository")
 */
class Chart
{
    /**
     * @var string
     *
     * @ORM\Column(name="libRecord_fr", type="string", length=100, nullable=true)
     */
    private $libRecord_fr;

    /**
     * @var string
     *
     * @ORM\Column(name="libRecord_en", type="string", length=100, nullable=false)
     */
    private $libRecord_en;

    /**
     * @var string
     *
     * @ORM\Column(name="statut", type="string", nullable=false)
     */
    private $statut;

    /**
     * @var string
     *
     * @ORM\Column(name="statutTeam", type="string", nullable=false)
     */
    private $statutTeam;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPost", type="integer", nullable=false)
     */
    private $nbPost;

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
     * @ORM\Column(name="idRecord", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idRecord;

    /**
     * @var VideoGamesRecords\CoreBundle\Entity\Group
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Group")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGroupe", referencedColumnName="idGroupe")
     * })
     */
    private $idGroupe;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\ManyToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\Member", inversedBy="idRecord")
     * @ORM\JoinTable(name="vgr_record_membre",
     *   joinColumns={
     *     @ORM\JoinColumn(name="idRecord", referencedColumnName="idRecord")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="idMembre", referencedColumnName="idMembre")
     *   }
     * )
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
     * Set libRecord_fr
     *
     * @param string $libRecordFr
     * @return Chart
     */
    public function setLibRecordFr($libRecordFr)
    {
        $this->libRecord_fr = $libRecordFr;

        return $this;
    }

    /**
     * Get libRecord_fr
     *
     * @return string 
     */
    public function getLibRecordFr()
    {
        return $this->libRecord_fr;
    }

    /**
     * Set libRecord_en
     *
     * @param string $libRecordEn
     * @return Chart
     */
    public function setLibRecordEn($libRecordEn)
    {
        $this->libRecord_en = $libRecordEn;

        return $this;
    }

    /**
     * Get libRecord_en
     *
     * @return string 
     */
    public function getLibRecordEn()
    {
        return $this->libRecord_en;
    }

    /**
     * Set statut
     *
     * @param string $statut
     * @return Chart
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
     * Set statutTeam
     *
     * @param string $statutTeam
     * @return Chart
     */
    public function setStatutTeam($statutTeam)
    {
        $this->statutTeam = $statutTeam;

        return $this;
    }

    /**
     * Get statutTeam
     *
     * @return string 
     */
    public function getStatutTeam()
    {
        return $this->statutTeam;
    }

    /**
     * Set nbPost
     *
     * @param integer $nbPost
     * @return Chart
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
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return Chart
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
     * @return Chart
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
     * Get idRecord
     *
     * @return integer 
     */
    public function getIdRecord()
    {
        return $this->idRecord;
    }

    /**
     * Set idGroupe
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Group $idGroupe
     * @return Chart
     */
    public function setIdGroupe(\Vgr\DefaultBundle\Entity\Group $idGroupe = null)
    {
        $this->idGroupe = $idGroupe;

        return $this;
    }

    /**
     * Get idGroupe
     *
     * @return \VideoGamesRecords\CoreBundle\Entity\Group
     */
    public function getIdGroupe()
    {
        return $this->idGroupe;
    }

    /**
     * Add idMembre
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Member $idMembre
     * @return Chart
     */
    public function addIdMembre(\VideoGamesRecords\CoreBundle\Entity\Member $idMembre)
    {
        $this->idMembre[] = $idMembre;

        return $this;
    }

    /**
     * Remove idMembre
     *
     * @param \VideoGamesRecords\CoreBundle\Entity\Member $idMembre
     */
    public function removeIdMembre(\VideoGamesRecords\CoreBundle\Entity\Member $idMembre)
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
