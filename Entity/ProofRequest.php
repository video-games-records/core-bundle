<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;

/**
 * Request
 *
 * @ORM\Table(name="vgr_proof_request", indexes={@ORM\Index(name="idxIdRequest", columns={"idRequest"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ProofRequestRepository")
 */
class ProofRequest
{
    use Timestampable;

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
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_IN_PROGRESS;

    /**
     * @var string
     * @ORM\Column(name="message", type="text", nullable=true)
     */
    private $message;

    /**
     * @var \DateTime
     * @ORM\Column(name="dateAcceptance", type="datetime", nullable=true)
     */
    private $dateAcceptance;

    /**
     * @var PlayerChart
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerChart", referencedColumnName="id", nullable=false)
     * })
     */
    private $playerChart;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerRequesting", referencedColumnName="id", nullable=false)
     * })
     */
    private $playerRequesting;

    /**
     * @var Player
     *
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayerResponding", referencedColumnName="id", nullable=false)
     * })
     */
    private $playerResponding;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Request [%s]', $this->id);
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId($id)
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
     * Set dateAcceptance
     *
     * @param \DateTime $dateAcceptance
     * @return $this
     */
    public function setDateAcceptance($dateAcceptance)
    {
        $this->dateAcceptance = $dateAcceptance;
        return $this;
    }

    /**
     * Get dateAcceptance
     *
     * @return \DateTime
     */
    public function getDateAcceptance()
    {
        return $this->dateAcceptance;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set playerCHart
     *
     * @param PlayerChart $playerChart
     * @return $this
     */
    public function setPlayerChart(PlayerChart $playerChart)
    {
        $this->playerChart = $playerChart;

        return $this;
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
     * Set playerRequesting
     *
     * @param Player $playerRequesting
     * @return $this
     */
    public function setPlayerRequesting(Player $playerRequesting)
    {
        $this->playerRequesting = $playerRequesting;

        return $this;
    }

    /**
     * Get playerRequesting
     *
     * @return Player
     */
    public function getPlayerRequesting()
    {
        return $this->playerRequesting;
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
