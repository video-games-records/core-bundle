<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * Proof
 *
 * @ORM\Table(name="vgr_proof", indexes={@ORM\Index(name="idxIdProof", columns={"idProof"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ProofRepository")
 */
class Proof implements TimestampableInterface
{
    use TimestampableTrait;

    const STATUS_IN_PROGRESS = 'IN PROGRESS';
    const STATUS_REFUSED = 'REFUSED';
    const STATUS_ACCEPTED = 'ACCEPTED';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Picture
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Picture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPicture", referencedColumnName="id", nullable=true)
     * })
     */
    private $picture;

    /**
     * @var Video
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Video")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idVideo", referencedColumnName="id", nullable=true)
     * })
     */
    private $video;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_IN_PROGRESS;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerResponding", referencedColumnName="id", nullable=true)
     * })
     */
    private $playerResponding;

    /**
     * @ORM\OneToOne(targetEntity="\VideoGamesRecords\CoreBundle\Entity\PlayerChart", mappedBy="proof")
     */
    private $playerChart;

    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Proof [%s]', $this->id);
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setIdProof($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set picture
     *
     * @param Picture $picture
     * @return $this
     */
    public function setPicture(Picture $picture)
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * Get picture
     *
     * @return Picture
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set video
     *
     * @param Video $video
     * @return $this
     */
    public function setVideo(Video $video)
    {
        $this->video= $video;
        return $this;
    }

    /**
     * Get video
     *
     * @return Video
     */
    public function getVideo()
    {
        return $this->video;
    }


    /**
     * Set status
     *
     * @param string $status
     * @return $this
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
     * Set playerResponding
     *
     * @param Player $playerResponding
     * @return $this
     */
    public function setPlayerResponding(Player $playerResponding)
    {
        $this->playerResponding = $playerResponding;

        return $this;
    }

    /**
     * Get playerResponding
     *
     * @return Player
     */
    public function getPlayerResponding()
    {
        return $this->playerResponding;
    }


    /**
     * Get playerChart
     *
     * @return PlayerChart
     */
    public function getPlayerChart()
    {
        return $this->playerChart;
    }



    /**
     * @return array
     */
    public static function getStatusChoices()
    {
        return [
            self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
            self::STATUS_REFUSED => self::STATUS_REFUSED,
            self::STATUS_ACCEPTED => self::STATUS_ACCEPTED,
        ];
    }
}
