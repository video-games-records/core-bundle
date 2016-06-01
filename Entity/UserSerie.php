<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserSerie
 *
 * @ORM\Table(name="mv_membre_serie", indexes={@ORM\Index(name="idxIdSerie", columns={"idSerie"}), @ORM\Index(name="idxIdMembre", columns={"idMembre"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserSerieRepository")
 */
class UserSerie
{

    /**
     * @ORM\Column(name="idMembre", type="integer")
     * @ORM\Id
     */
    private $idMembre;

    /**
     * @ORM\Column(name="idSerie", type="integer")
     * @ORM\Id
     */
    private $idSerie;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idMembre", referencedColumnName="idMembre")
     * })
     */
    private $user;

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
     * @var integer
     *
     * @ORM\Column(name="rankPoint", type="integer", nullable=false)
     */
    private $rankPoint;

    /**
     * @var integer
     *
     * @ORM\Column(name="rankMedal", type="integer", nullable=false)
     */
    private $rankMedal;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank0", type="integer", nullable=false)
     */
    private $rank0;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank1", type="integer", nullable=false)
     */
    private $rank1;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank2", type="integer", nullable=false)
     */
    private $rank2;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank3", type="integer", nullable=false)
     */
    private $rank3;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank4", type="integer", nullable=false)
     */
    private $rank4;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank5", type="integer", nullable=false)
     */
    private $rank5;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointRecord", type="integer", nullable=false)
     */
    private $pointRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointRecordSansDLC", type="integer", nullable=false)
     */
    private $pointRecordSansDLC;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbRecord", type="integer", nullable=false)
     */
    private $nbRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbRecordProuve", type="integer", nullable=false)
     */
    private $nbRecordProuve;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbRecordSansDLC", type="integer", nullable=false)
     */
    private $nbRecordSansDLC;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbRecordProuveSansDLC", type="integer", nullable=false)
     */
    private $nbRecordProuveSansDLC;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointJeu", type="integer", nullable=false)
     */
    private $pointJeu;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbJeu", type="integer", nullable=false)
     */
    private $nbJeu;


    /**
     * Set idMembre
     *
     * @param integer $idMembre
     * @return UserSerie
     */
    public function setIdMembre($idMembre)
    {
        $this->idMembre = $idMembre;
        return $this;
    }

    /**
     * Get idMembre
     *
     * @return integer
     */
    public function getIdMembre()
    {
        return $this->idMembre;
    }

    /**
     * Set idSerie
     *
     * @param integer $idSerie
     * @return UserSerie
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
     * Set rankPoint
     *
     * @param integer $rankPoint
     * @return UserSerie
     */
    public function setRankPoint($rankPoint)
    {
        $this->rankPoint = $rankPoint;
        return $this;
    }

    /**
     * Get rankPoint
     *
     * @return integer
     */
    public function getRankPoint()
    {
        return $this->rankPoint;
    }

    /**
     * Set rankMedal
     *
     * @param integer $rankMedal
     * @return UserSerie
     */
    public function setRankMedal($rankMedal)
    {
        $this->rankMedal = $rankMedal;
        return $this;
    }

    /**
     * Get rankMedal
     *
     * @return integer
     */
    public function getRankMedal()
    {
        return $this->rankMedal;
    }

    /**
     * Set rank0
     *
     * @param integer $rank0
     * @return UserSerie
     */
    public function setRank0($rank0)
    {
        $this->rank0 = $rank0;
        return $this;
    }

    /**
     * Get rank0
     *
     * @return integer
     */
    public function getRank0()
    {
        return $this->rank0;
    }

    /**
     * Set rank1
     *
     * @param integer $rank1
     * @return UserSerie
     */
    public function setRank1($rank1)
    {
        $this->rank1 = $rank1;
        return $this;
    }

    /**
     * Get rank1
     *
     * @return integer
     */
    public function getRank1()
    {
        return $this->rank1;
    }

    /**
     * Set rank2
     *
     * @param integer $rank2
     * @return UserSerie
     */
    public function setRank2($rank2)
    {
        $this->rank2 = $rank2;
        return $this;
    }

    /**
     * Get rank2
     *
     * @return integer
     */
    public function getRank2()
    {
        return $this->rank2;
    }

    /**
     * Set rank3
     *
     * @param integer $rank3
     * @return UserSerie
     */
    public function setRank3($rank3)
    {
        $this->rank3 = $rank3;
        return $this;
    }

    /**
     * Get rank3
     *
     * @return integer
     */
    public function getRank3()
    {
        return $this->rank3;
    }

    /**
     * Set rank4
     *
     * @param integer $rank4
     * @return UserSerie
     */
    public function setRank4($rank4)
    {
        $this->rank4 = $rank4;
        return $this;
    }

    /**
     * Get rank4
     *
     * @return integer
     */
    public function getRank4()
    {
        return $this->rank4;
    }

    /**
     * Set rank5
     *
     * @param integer $rank5
     * @return UserSerie
     */
    public function setRank5($rank5)
    {
        $this->rank5 = $rank5;
        return $this;
    }

    /**
     * Get rank5
     *
     * @return integer
     */
    public function getRank5()
    {
        return $this->rank5;
    }

    /**
     * Set pointRecord
     *
     * @param integer $pointRecord
     * @return UserSerie
     */
    public function setPointRecord($pointRecord)
    {
        $this->pointRecord = $pointRecord;
        return $this;
    }

    /**
     * Get pointRecord
     *
     * @return integer
     */
    public function getPointRecord()
    {
        return $this->pointRecord;
    }

    /**
     * Set pointRecordSansDLC
     *
     * @param integer $pointRecordSansDLC
     * @return UserGame
     */
    public function setPointRecordSansDLC($pointRecordSansDLC)
    {
        $this->pointRecordSansDLC = $pointRecordSansDLC;
        return $this;
    }

    /**
     * Get pointRecordSansDLC
     *
     * @return integer
     */
    public function getPointRecordSansDLC()
    {
        return $this->pointRecordSansDLC;
    }


    /**
     * Set nbRecord
     *
     * @param integer $nbRecord
     * @return UserSerie
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
     * Set nbRecordProuve
     *
     * @param integer $nbRecordProuve
     * @return UserSerie
     */
    public function setNbRecordProuve($nbRecordProuve)
    {
        $this->nbRecordProuve = $nbRecordProuve;
        return $this;
    }

    /**
     * Get nbRecordProuve
     *
     * @return integer
     */
    public function getNbRecordProuve()
    {
        return $this->nbRecordProuve;
    }

    /**
     * Set nbRecordSansDLC
     *
     * @param integer $nbRecordSansDLC
     * @return UserGame
     */
    public function setNbRecordSansDLC($nbRecordSansDLC)
    {
        $this->nbRecordSansDLC = $nbRecordSansDLC;
        return $this;
    }

    /**
     * Get nbRecordSansDLC
     *
     * @return integer
     */
    public function getNbRecordv()
    {
        return $this->nbRecordSansDLC;
    }


    /**
     * Set nbRecordProuveSansDLC
     *
     * @param integer $nbRecordProuveSansDLC
     * @return UserGame
     */
    public function setNbRecordProuveSansDLC($nbRecordProuveSansDLC)
    {
        $this->nbRecordProuveSansDLC = $nbRecordProuveSansDLC;
        return $this;
    }

    /**
     * Get nbRecordProuveSansDLC
     *
     * @return integer
     */
    public function getNbRecordProuveSansDLC()
    {
        return $this->nbRecordProuveSansDLC;
    }

    /**
     * Set pointJeu
     *
     * @param integer $pointJeu
     * @return UserGame
     */
    public function setPointJeu($pointJeu)
    {
        $this->pointJeu = $pointJeu;
        return $this;
    }

    /**
     * Get pointJeu
     *
     * @return integer
     */
    public function getPointJeu()
    {
        return $this->pointJeu;
    }


    /**
     * Set nbJeu
     *
     * @param integer $nbJeu
     * @return UserGame
     */
    public function setNbJeu($nbJeu)
    {
        $this->nbJeu = $nbJeu;
        return $this;
    }

    /**
     * Get nbJeu
     *
     * @return integer
     */
    public function getNbJeu()
    {
        return $this->nbJeu;
    }


    /**
     * Set serie
     *
     * @param Serie $serie
     * @return UserSerie
     */
    public function setSerie(Serie $serie = null)
    {
        $this->serie = $serie;
        $this->setIdSerie($serie->getIdSerie());
        return $this;
    }

    /**
     * Get serie
     *
     * @return Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }


    /**
     * Set user
     *
     * @param User $user
     * @return UserSerie
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;
        $this->setIdMembre($user->getIdMembre());
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }


    /**
     * @return string
     */
    public function getPointsBackgroundColor()
    {
        $class = array(
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        );

        if ($this->getRankPoint() <= 3) {
            return sprintf("class=\"%s\"",$class[$this->getRankPoint()]);
        } else {
            return '';
        }
    }

    /**
     * @return string
     */
    public function getMedalsBackgroundColor()
    {
        $class = array(
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        );

        if ($this->getRankMedal() <= 3) {
            return sprintf("class=\"%s\"",$class[$this->getRankMedal()]);
        } else {
            return '';
        }
    }

}
