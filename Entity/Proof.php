<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use DateTime;

/**
 * Proof
 *
 * @ORM\Table(name="vgr_proof")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ProofRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\ProofListener"})
 */
class Proof implements TimestampableInterface
{
    use TimestampableTrait;

    const STATUS_IN_PROGRESS = 'IN PROGRESS';
    const STATUS_REFUSED = 'REFUSED';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_DELETED = 'DELETED';


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
     * @var string
     * @ORM\Column(name="response", type="text", nullable=true)
     */
    private $response;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="proofRespondings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerResponding", referencedColumnName="id", nullable=true)
     * })
     */
    private $playerResponding;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", inversedBy="proofs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private $player;

    /**
     * @var Chart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false)
     * })
     */
    private $chart;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="checked_at", type="datetime", nullable=true)
     */
    private $checkedAt;

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
     * @param integer $id
     * @return $this
     */
    public function setId(int $id)
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
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status)
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
     * Set response
     *
     * @param string $response
     * @return $this
     */
    public function setResponse(string $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get response
     *
     * @return string
     */
    public function getResponse()
    {
        return $this->response;
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
     * Set player
     *
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * Set chart
     *
     * @param Chart $chart
     * @return $this
     */
    public function setChart(Chart $chart)
    {
        $this->chart = $chart;

        return $this;
    }

    /**
     * Get chart
     *
     * @return Chart
     */
    public function getChart()
    {
        return $this->chart;
    }

     /**
     * Set checkedAt
     *
     * @param DateTime $checkedAt
     * @return $this
     */
    public function setCheckedAt(DateTime $checkedAt)
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }

    /**
     * Get checkedAt
     *
     * @return DateTime
     */
    public function getCheckedAt()
    {
        return $this->checkedAt;
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
     * @return string
     */
    public function getType(): string
    {
        return ($this->getPicture() != null) ? 'Picture' : 'Video';
    }

    /**
     * @return array
     */
    public static function getStatusChoices()
    {
        return [
            'label.status.inProgress' => self::STATUS_IN_PROGRESS,
            'label.status.refused' => self::STATUS_REFUSED,
            'label.status.accepted' => self::STATUS_ACCEPTED,
            'label.status.closed' => self::STATUS_CLOSED
        ];
    }
}
