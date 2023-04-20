<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * Request
 * @ORM\Table(name="vgr_proof_request")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ProofRequestRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\ProofRequestListener"})
 */
class ProofRequest
{
    use TimestampableEntity;

    const STATUS_IN_PROGRESS = 'IN PROGRESS';
    const STATUS_REFUSED = 'REFUSED';
    const STATUS_ACCEPTED = 'ACCEPTED';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="status", type="string", length=50, nullable=false)
     */
    private string $status = self::STATUS_IN_PROGRESS;

    /**
     * @ORM\Column(name="response", type="text", nullable=true)
     */
    private ?string $response = null;

    /**
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private ?string $message = null;

    /**
     * @ORM\Column(name="dateAcceptance", type="datetime", nullable=true)
     */
    private ?Datetime $dateAcceptance = null;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerChart", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private PlayerChart $playerChart;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerRequesting", referencedColumnName="id", nullable=false)
     * })
     */
    private Player $playerRequesting;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerResponding", referencedColumnName="id", nullable=true)
     * })
     */
    private ?Player $playerResponding = null;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Request [%s]', $this->id);
    }


    /**
     * Set id
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): self
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
     * Set status
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): self
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
     * @return $this
     */
    public function setResponse(string $response): self
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
     * Set dateAcceptance
     * @param DateTime $dateAcceptance
     * @return $this
     */
    public function setDateAcceptance(DateTime $dateAcceptance): self
    {
        $this->dateAcceptance = $dateAcceptance;
        return $this;
    }

    /**
     * Get dateAcceptance
     * @return DateTime
     */
    public function getDateAcceptance(): ?DateTime
    {
        return $this->dateAcceptance;
    }

    /**
     * Set message
     * @param string $message
     * @return $this
     */
    public function setMessage(string $message): self
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     * @return string
     */
    public function getMessage(): ?string
    {
        return $this->message;
    }

    /**
     * Set playerCHart
     * @param PlayerChart $playerChart
     * @return $this
     */
    public function setPlayerChart(PlayerChart $playerChart): self
    {
        $this->playerChart = $playerChart;

        return $this;
    }

    /**
     * Get playerChart
     * @return PlayerChart
     */
    public function getPlayerChart(): PlayerChart
    {
        return $this->playerChart;
    }

    /**
     * Set playerRequesting
     * @param Player $playerRequesting
     * @return $this
     */
    public function setPlayerRequesting(Player $playerRequesting): self
    {
        $this->playerRequesting = $playerRequesting;

        return $this;
    }

    /**
     * Get playerRequesting
     * @return Player
     */
    public function getPlayerRequesting(): Player
    {
        return $this->playerRequesting;
    }

    /**
     * Set playerResponding
     * @param Player|null $playerResponding
     * @return $this
     */
    public function setPlayerResponding(Player $playerResponding = null): self
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
     * @return array
     */
    public static function getStatusChoices(): array
    {
        return [
            self::STATUS_IN_PROGRESS => self::STATUS_IN_PROGRESS,
            self::STATUS_REFUSED => self::STATUS_REFUSED,
            self::STATUS_ACCEPTED => self::STATUS_ACCEPTED,
        ];
    }
}
