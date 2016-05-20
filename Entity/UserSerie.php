<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MemberGame
 *
 * @ORM\Table(name="mv_membre_serie", indexes={@ORM\Index(name="idxIdSerie", columns={"idSerie"}), @ORM\Index(name="idxIdMembre", columns={"idMembre"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserSerieRepository")
 */
class UserSerie
{

    /**
     * This columns are missing on this entity
     *  - pointJeu
     *  - pointRecordSansDLC
     *  - nbRecordSansDLC
     *  - nbRecordProuveSansDLC
     */


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
     * Set rank
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
     * Set rank
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
     * Set serie
     *
     * @param Serie $serie
     * @return UserSerie
     */
    public function setSerie(Serie $serie = null)
    {
        $this->serie = $serie;
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
