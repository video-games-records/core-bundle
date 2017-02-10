<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Table(name="vgr_player", indexes={@ORM\Index(name="vgr_pointJeu", columns={"vgr_pointJeu"}), @ORM\Index(name="vgr_rank_pointJeu", columns={"vgr_rank_pointJeu"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerRepository")
 */
class Player
{
    /**
     * @var \AppBundle\Entity\User
     *
     * @ORM\OneToOne(targetEntity="AppBundle\Entity\User")
     * @ORM\JoinColumn(name="normandie_user_id", referencedColumnName="id")
     */
    private $normandieUser;

    /**
     * @var integer
     *
     * @ORM\Column(name="idPlayer", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPlayer;

    /**
     * @var string
     *
     * @Assert\Length(max="50")
     * @ORM\Column(name="pseudo", type="string", length=50, nullable=false)
     */
    private $pseudo;

    /**
     * @var string
     *
     * @ORM\Column(name="avatar", type="string", length=100, nullable=false)
     */
    private $avatar = 'default.jpg';

    /**
     * @var string
     *
     * @ORM\Column(name="vgr_gamerCard", type="string", length=50, nullable=true)
     */
    private $vgr_gamerCard;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vgr_displayGamerCard", type="boolean", nullable=false)
     */
    private $vgr_displayGamerCard = true;

    /**
     * @var boolean
     *
     * @ORM\Column(name="vgr_displayGoalBar", type="boolean", nullable=false)
     */
    private $vgr_displayGoalBar = true;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank0", type="integer", nullable=true)
     */
    private $vgr_rank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank1", type="integer", nullable=true)
     */
    private $vgr_rank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank2", type="integer", nullable=true)
     */
    private $vgr_rank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank3", type="integer", nullable=true)
     */
    private $vgr_rank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointRecord", type="integer", nullable=false)
     */
    private $vgr_pointRecord = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointVGR", type="integer", nullable=false)
     */
    private $vgr_pointVGR = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointBadge", type="integer", nullable=false)
     */
    private $vgr_pointBadge = 0;

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
     * @ORM\Column(name="vgr_rank_cup", type="integer", nullable=true)
     */
    private $vgr_rank_cup;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank0", type="integer", nullable=true)
     */
    private $vgr_cup_rank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank1", type="integer", nullable=true)
     */
    private $vgr_cup_rank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank2", type="integer", nullable=true)
     */
    private $vgr_cup_rank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_cup_rank3", type="integer", nullable=true)
     */
    private $vgr_cup_rank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_nbRecord", type="integer", nullable=false)
     */
    private $vgr_nbRecord = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_nbRecordProuve", type="integer", nullable=false)
     */
    private $vgr_nbRecordProuve = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_nbMasterBadge", type="integer", nullable=false)
     */
    private $vgr_nbMasterBadge = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_pointJeu", type="integer", nullable=false)
     */
    private $vgr_pointJeu = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="vgr_rank_pointJeu", type="integer", nullable=true)
     */
    private $vgr_rank_pointJeu;

    /**
     * Set idPlayer
     *
     * @param integer $idPlayer
     * @return Player
     */
    public function setIdPlayer($idPlayer)
    {
        $this->idPlayer = $idPlayer;
        return $this;
    }

    /**
     * Get idPlayer
     *
     * @return integer
     */
    public function getIdPlayer()
    {
        return $this->idPlayer;
    }

    /**
     * Set pseudo
     *
     * @param string $pseudo
     * @return Player
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
     * Set avatar
     *
     * @param string $avatar
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return Player
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
     * @return \AppBundle\Entity\User
     */
    public function getNormandieUser()
    {
        return $this->normandieUser;
    }

    /**
     * @param \AppBundle\Entity\User $normandieUser
     * @return Player
     */
    public function setNormandieUser($normandieUser)
    {
        $this->normandieUser = $normandieUser;
        return $this;
    }
}
