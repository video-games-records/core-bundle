<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 *
 * @ORM\Table(name="t_membre", uniqueConstraints={@ORM\UniqueConstraint(name="pseudo", columns={"pseudo"})}, indexes={@ORM\Index(name="nom", columns={"nom"}), @ORM\Index(name="prenom", columns={"prenom"}), @ORM\Index(name="idPays", columns={"idPays"}), @ORM\Index(name="vgr_pointJeu", columns={"vgr_pointJeu"}), @ORM\Index(name="vgr_rank_pointJeu", columns={"vgr_rank_pointJeu"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\MemberRepository")
 */
class User
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idUser", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idUser;


    /**
     * @var string
     *
     * @ORM\Column(name="pseudo", type="string", length=20, nullable=false)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50, nullable=true)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=40, nullable=true)
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=100, nullable=false)
     */
    private $avatar;

    /**
     * @var string
     *
     * @ORM\Column(name="vgr_gamerCard", type="string", length=50, nullable=false)
     */
    private $vgr_gamerCard;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vgr_displayGamerCard", type="boolean", nullable=false)
     */
    private $vgr_displayGamerCard;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vgr_displayGoalBar", type="boolean", nullable=false)
     */
    private $vgr_displayGoalBar;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank0", type="integer", nullable=false)
     */
    private $vgr_rank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank1", type="integer", nullable=false)
     */
    private $vgr_rank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank2", type="integer", nullable=false)
     */
    private $vgr_rank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank3", type="integer", nullable=false)
     */
    private $vgr_rank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointRecord", type="integer", nullable=false)
     */
    private $vgr_pointRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointVGR", type="integer", nullable=false)
     */
    private $vgr_pointVGR;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointBadge", type="integer", nullable=false)
     */
    private $vgr_pointBadge;

    /**
     * @var string
     *
     * @ORM\Column(name="vgr_collection", type="text", length=65535, nullable=true)
     */
    private $vgr_collection;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank_point", type="integer", nullable=true)
     */
    private $vgr_rank_point;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank_medal", type="integer", nullable=true)
     */
    private $vgr_rank_medal;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank_proof", type="integer", nullable=true)
     */
    private $vgr_rank_proof;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank_badge", type="integer", nullable=true)
     */
    private $vgr_rank_badge;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank_cup", type="integer", nullable=false)
     */
    private $vgr_rank_cup;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank0", type="integer", nullable=false)
     */
    private $vgr_cup_rank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank1", type="integer", nullable=false)
     */
    private $vgr_cup_rank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank2", type="integer", nullable=false)
     */
    private $vgr_cup_rank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank3", type="integer", nullable=false)
     */
    private $vgr_cup_rank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_nbRecord", type="integer", nullable=false)
     */
    private $vgr_nbRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_nbRecordProuve", type="integer", nullable=false)
     */
    private $vgr_nbRecordProuve;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_nbMasterBadge", type="integer", nullable=false)
     */
    private $vgr_nbMasterBadge;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointJeu", type="integer", nullable=false)
     */
    private $vgr_pointJeu;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank_pointJeu", type="integer", nullable=false)
     */
    private $vgr_rank_pointJeu;

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
     * @var Country
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPays", referencedColumnName="idPays")
     * })
     */
    private $idPays;


    /**
     * Set idUser
     *
     * @param integer $idUser
     * @return User
     */
    public function setIdUser($idUser)
    {
        $this->idUser = $idUser;
        return $this;
    }

    /**
     * Get idUser
     *
     * @return integer
     */
    public function getIdUser()
    {
        return $this->idUser;
    }


    /**
     * Set pseudo
     *
     * @param string $pseudo
     * @return User
     */
    public function setPseudo($pseudo)
    {
        $this->pseudo = $pseudo;

        return $this;
    }

    /**
     * Get pseudo
     *
     * @return string
     */
    public function getPseudo()
    {
        return $this->pseudo;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set nom
     *
     * @param string $nom
     * @return User
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set prenom
     *
     * @param string $prenom
     * @return User
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set avatar
     *
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * Get avatar
     *
     * @return string
     */
    public function getAvatar()
    {
        return $this->avatar;
    }

    /**
     * Set vgr_gamerCard
     *
     * @param string $vgrGamerCard
     * @return User
     */
    public function setVgrGamerCard($vgrGamerCard)
    {
        $this->vgr_gamerCard = $vgrGamerCard;

        return $this;
    }

    /**
     * Get vgr_gamerCard
     *
     * @return string
     */
    public function getVgrGamerCard()
    {
        return $this->vgr_gamerCard;
    }

    /**
     * Set vgr_displayGamerCard
     *
     * @param boolean $vgrDisplayGamerCard
     * @return User
     */
    public function setVgrDisplayGamerCard($vgrDisplayGamerCard)
    {
        $this->vgr_displayGamerCard = $vgrDisplayGamerCard;

        return $this;
    }

    /**
     * Get vgr_displayGamerCard
     *
     * @return boolean
     */
    public function getVgrDisplayGamerCard()
    {
        return $this->vgr_displayGamerCard;
    }

    /**
     * Set vgr_displayGoalBar
     *
     * @param boolean $vgrDisplayGoalBar
     * @return User
     */
    public function setVgrDisplayGoalBar($vgrDisplayGoalBar)
    {
        $this->vgr_displayGoalBar = $vgrDisplayGoalBar;

        return $this;
    }

    /**
     * Get vgr_displayGoalBar
     *
     * @return boolean
     */
    public function getVgrDisplayGoalBar()
    {
        return $this->vgr_displayGoalBar;
    }

    /**
     * Set vgr_rank0
     *
     * @param integer $vgrRank0
     * @return User
     */
    public function setVgrRank0($vgrRank0)
    {
        $this->vgr_rank0 = $vgrRank0;

        return $this;
    }

    /**
     * Get vgr_rank0
     *
     * @return integer
     */
    public function getVgrRank0()
    {
        return $this->vgr_rank0;
    }

    /**
     * Set vgr_rank1
     *
     * @param integer $vgrRank1
     * @return User
     */
    public function setVgrRank1($vgrRank1)
    {
        $this->vgr_rank1 = $vgrRank1;

        return $this;
    }

    /**
     * Get vgr_rank1
     *
     * @return integer
     */
    public function getVgrRank1()
    {
        return $this->vgr_rank1;
    }

    /**
     * Set vgr_rank2
     *
     * @param integer $vgrRank2
     * @return User
     */
    public function setVgrRank2($vgrRank2)
    {
        $this->vgr_rank2 = $vgrRank2;

        return $this;
    }

    /**
     * Get vgr_rank2
     *
     * @return integer
     */
    public function getVgrRank2()
    {
        return $this->vgr_rank2;
    }

    /**
     * Set vgr_rank3
     *
     * @param integer $vgrRank3
     * @return User
     */
    public function setVgrRank3($vgrRank3)
    {
        $this->vgr_rank3 = $vgrRank3;

        return $this;
    }

    /**
     * Get vgr_rank3
     *
     * @return integer
     */
    public function getVgrRank3()
    {
        return $this->vgr_rank3;
    }

    /**
     * Set vgr_pointRecord
     *
     * @param integer $vgrPointRecord
     * @return User
     */
    public function setVgrPointRecord($vgrPointRecord)
    {
        $this->vgr_pointRecord = $vgrPointRecord;

        return $this;
    }

    /**
     * Get vgr_pointRecord
     *
     * @return integer
     */
    public function getVgrPointRecord()
    {
        return $this->vgr_pointRecord;
    }

    /**
     * Set vgr_pointVGR
     *
     * @param integer $vgrPointVGR
     * @return User
     */
    public function setVgrPointVGR($vgrPointVGR)
    {
        $this->vgr_pointVGR = $vgrPointVGR;

        return $this;
    }

    /**
     * Get vgr_pointVGR
     *
     * @return integer
     */
    public function getVgrPointVGR()
    {
        return $this->vgr_pointVGR;
    }

    /**
     * Set vgr_pointBadge
     *
     * @param integer $vgrPointBadge
     * @return User
     */
    public function setVgrPointBadge($vgrPointBadge)
    {
        $this->vgr_pointBadge = $vgrPointBadge;

        return $this;
    }

    /**
     * Get vgr_pointBadge
     *
     * @return integer
     */
    public function getVgrPointBadge()
    {
        return $this->vgr_pointBadge;
    }

    /**
     * Set vgr_collection
     *
     * @param string $vgrCollection
     * @return User
     */
    public function setVgrCollection($vgrCollection)
    {
        $this->vgr_collection = $vgrCollection;

        return $this;
    }

    /**
     * Get vgr_collection
     *
     * @return string
     */
    public function getVgrCollection()
    {
        return $this->vgr_collection;
    }

    /**
     * Set vgr_rank_point
     *
     * @param integer $vgrRankPoint
     * @return User
     */
    public function setVgrRankPoint($vgrRankPoint)
    {
        $this->vgr_rank_point = $vgrRankPoint;

        return $this;
    }

    /**
     * Get vgr_rank_point
     *
     * @return integer
     */
    public function getVgrRankPoint()
    {
        return $this->vgr_rank_point;
    }

    /**
     * Set vgr_rank_medal
     *
     * @param integer $vgrRankMedal
     * @return User
     */
    public function setVgrRankMedal($vgrRankMedal)
    {
        $this->vgr_rank_medal = $vgrRankMedal;

        return $this;
    }

    /**
     * Get vgr_rank_medal
     *
     * @return integer
     */
    public function getVgrRankMedal()
    {
        return $this->vgr_rank_medal;
    }

    /**
     * Set vgr_rank_proof
     *
     * @param integer $vgrRankProof
     * @return User
     */
    public function setVgrRankProof($vgrRankProof)
    {
        $this->vgr_rank_proof = $vgrRankProof;

        return $this;
    }

    /**
     * Get vgr_rank_proof
     *
     * @return integer
     */
    public function getVgrRankProof()
    {
        return $this->vgr_rank_proof;
    }

    /**
     * Set vgr_rank_badge
     *
     * @param integer $vgrRankBadge
     * @return User
     */
    public function setVgrRankBadge($vgrRankBadge)
    {
        $this->vgr_rank_badge = $vgrRankBadge;

        return $this;
    }

    /**
     * Get vgr_rank_badge
     *
     * @return integer
     */
    public function getVgrRankBadge()
    {
        return $this->vgr_rank_badge;
    }

    /**
     * Set vgr_rank_cup
     *
     * @param integer $vgrRankCup
     * @return User
     */
    public function setVgrRankCup($vgrRankCup)
    {
        $this->vgr_rank_cup = $vgrRankCup;

        return $this;
    }

    /**
     * Get vgr_rank_cup
     *
     * @return integer
     */
    public function getVgrRankCup()
    {
        return $this->vgr_rank_cup;
    }

    /**
     * Set vgr_cup_rank0
     *
     * @param integer $vgrCupRank0
     * @return User
     */
    public function setVgrCupRank0($vgrCupRank0)
    {
        $this->vgr_cup_rank0 = $vgrCupRank0;

        return $this;
    }

    /**
     * Get vgr_cup_rank0
     *
     * @return integer
     */
    public function getVgrCupRank0()
    {
        return $this->vgr_cup_rank0;
    }

    /**
     * Set vgr_cup_rank1
     *
     * @param integer $vgrCupRank1
     * @return User
     */
    public function setVgrCupRank1($vgrCupRank1)
    {
        $this->vgr_cup_rank1 = $vgrCupRank1;

        return $this;
    }

    /**
     * Get vgr_cup_rank1
     *
     * @return integer
     */
    public function getVgrCupRank1()
    {
        return $this->vgr_cup_rank1;
    }

    /**
     * Set vgr_cup_rank2
     *
     * @param integer $vgrCupRank2
     * @return User
     */
    public function setVgrCupRank2($vgrCupRank2)
    {
        $this->vgr_cup_rank2 = $vgrCupRank2;

        return $this;
    }

    /**
     * Get vgr_cup_rank2
     *
     * @return integer
     */
    public function getVgrCupRank2()
    {
        return $this->vgr_cup_rank2;
    }

    /**
     * Set vgr_cup_rank3
     *
     * @param integer $vgrCupRank3
     * @return User
     */
    public function setVgrCupRank3($vgrCupRank3)
    {
        $this->vgr_cup_rank3 = $vgrCupRank3;

        return $this;
    }

    /**
     * Get vgr_cup_rank3
     *
     * @return integer
     */
    public function getVgrCupRank3()
    {
        return $this->vgr_cup_rank3;
    }

    /**
     * Set vgr_nbRecord
     *
     * @param integer $vgrNbRecord
     * @return User
     */
    public function setVgrNbRecord($vgrNbRecord)
    {
        $this->vgr_nbRecord = $vgrNbRecord;

        return $this;
    }

    /**
     * Get vgr_nbRecord
     *
     * @return integer
     */
    public function getVgrNbRecord()
    {
        return $this->vgr_nbRecord;
    }

    /**
     * Set vgr_nbRecordProuve
     *
     * @param integer $vgrNbRecordProuve
     * @return User
     */
    public function setVgrNbRecordProuve($vgrNbRecordProuve)
    {
        $this->vgr_nbRecordProuve = $vgrNbRecordProuve;

        return $this;
    }

    /**
     * Get vgr_nbRecordProuve
     *
     * @return integer
     */
    public function getVgrNbRecordProuve()
    {
        return $this->vgr_nbRecordProuve;
    }

    /**
     * Set vgr_nbMasterBadge
     *
     * @param integer $vgrNbMasterBadge
     * @return User
     */
    public function setVgrNbMasterBadge($vgrNbMasterBadge)
    {
        $this->vgr_nbMasterBadge = $vgrNbMasterBadge;

        return $this;
    }

    /**
     * Get vgr_nbMasterBadge
     *
     * @return integer
     */
    public function getVgrNbMasterBadge()
    {
        return $this->vgr_nbMasterBadge;
    }

    /**
     * Set vgr_pointJeu
     *
     * @param integer $vgrPointJeu
     * @return User
     */
    public function setVgrPointJeu($vgrPointJeu)
    {
        $this->vgr_pointJeu = $vgrPointJeu;

        return $this;
    }

    /**
     * Get vgr_pointJeu
     *
     * @return integer
     */
    public function getVgrPointJeu()
    {
        return $this->vgr_pointJeu;
    }

    /**
     * Set vgr_rank_pointJeu
     *
     * @param integer $vgrRankPointJeu
     * @return User
     */
    public function setVgrRankPointJeu($vgrRankPointJeu)
    {
        $this->vgr_rank_pointJeu = $vgrRankPointJeu;

        return $this;
    }

    /**
     * Get vgr_rank_pointJeu
     *
     * @return integer
     */
    public function getVgrRankPointJeu()
    {
        return $this->vgr_rank_pointJeu;
    }

    /**
     * Set dateCreation
     *
     * @param \DateTime $dateCreation
     * @return User
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
     * @return User
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
     * Set idPays
     *
     * @param Country $idPays
     * @return User
     */
    public function setIdPays(Country $idPays = null)
    {
        $this->idPays = $idPays;

        return $this;
    }

    /**
     * Get idPays
     *
     * @return Country
     */
    public function getIdPays()
    {
        return $this->idPays;
    }
}
