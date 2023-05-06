<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Proof
 * @ORM\Table(name="vgr_proof")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ProofRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\ProofListener"})
 */
class Proof
{
    use TimestampableEntity;

    const STATUS_IN_PROGRESS = 'IN PROGRESS';
    const STATUS_REFUSED = 'REFUSED';
    const STATUS_ACCEPTED = 'ACCEPTED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_DELETED = 'DELETED';


    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Picture")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPicture", referencedColumnName="id", nullable=true)
     * })
     */
    private ?Picture $picture = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Video")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idVideo", referencedColumnName="id", nullable=true)
     * })
     */
    private ?Video $video = null;

    /**
     * @ORM\Column(name="status", type="string", length=30, nullable=false)
     */
    private string $status = self::STATUS_IN_PROGRESS;

    /**
     * @ORM\Column(name="response", type="text", nullable=true)
     */
    private ?string $response = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerResponding", referencedColumnName="id", nullable=true)
     * })
     */
    private ?Player $playerResponding = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private Player $player;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Chart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idChart", referencedColumnName="id", nullable=false)
     * })
     */
    private Chart $chart;

    /**
     * @ORM\Column(name="checked_at", type="datetime", nullable=true)
     */
    private ?DateTime $checkedAt;

    /**
     * @ORM\OneToOne(targetEntity="\VideoGamesRecords\CoreBundle\Entity\PlayerChart", mappedBy="proof")
     */
    private ?PlayerChart $playerChart;

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
     * @return Proof
     */
    public function setId(int $id): Proof
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set picture
     * @param Picture $picture
     * @return Proof
     */
    public function setPicture(Picture $picture): Proof
    {
        $this->picture = $picture;
        return $this;
    }

    /**
     * Get picture
     * @return Picture
     */
    public function getPicture(): ?Picture
    {
        return $this->picture;
    }

    /**
     * Set video
     * @param Video $video
     * @return Proof
     */
    public function setVideo(Video $video): Proof
    {
        $this->video = $video;
        return $this;
    }

    /**
     * Get video
     * @return Video
     */
    public function getVideo(): ?Video
    {
        return $this->video;
    }


    /**
     * Set status
     * @param string $status
     * @return Proof
     */
    public function setStatus(string $status): Proof
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * Set response
     * @param string $response
     * @return Proof
     */
    public function setResponse(string $response): Proof
    {
        $this->response = $response;
        return $this;
    }

    /**
     * Get response
     * @return string
     */
    public function getResponse(): ?string
    {
        return $this->response;
    }

    /**
     * Set playerResponding
     * @param Player|null $playerResponding
     * @return Proof
     */
    public function setPlayerResponding(Player $playerResponding = null): Proof
    {
        $this->playerResponding = $playerResponding;

        return $this;
    }

    /**
     * Get playerResponding
     * @return Player|null
     */
    public function getPlayerResponding(): ?Player
    {
        return $this->playerResponding;
    }


    /**
     * Set player
     * @param Player $player
     * @return Proof
     */
    public function setPlayer(Player $player): Proof
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Set chart
     * @param Chart $chart
     * @return Proof
     */
    public function setChart(Chart $chart): Proof
    {
        $this->chart = $chart;

        return $this;
    }

    /**
     * Get chart
     * @return Chart
     */
    public function getChart(): Chart
    {
        return $this->chart;
    }

    /**
     * Set checkedAt
     * @param DateTime $checkedAt
     * @return Proof
     */
    public function setCheckedAt(DateTime $checkedAt): Proof
    {
        $this->checkedAt = $checkedAt;

        return $this;
    }

    /**
     * Get checkedAt
     * @return DateTime
     */
    public function getCheckedAt(): ?DateTime
    {
        return $this->checkedAt;
    }


    /**
     * Get playerChart
     * @return PlayerChart
     */
    public function getPlayerChart(): ?PlayerChart
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
    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
            self::STATUS_REFUSED => self::STATUS_REFUSED,
            self::STATUS_ACCEPTED => self::STATUS_ACCEPTED,
            self::STATUS_CLOSED => self::STATUS_CLOSED
        ];
    }
}
