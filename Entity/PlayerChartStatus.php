<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlayerChartStatus
 *
 * @ORM\Table(name="vgr_player_chart_status", indexes={@ORM\Index(name="idxIdStatus", columns={"idStatus"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository")
 * @ORM\HasLifecycleCallbacks()
 */
class PlayerChartStatus
{
    const ID_STATUS_NORMAL = 1;
    const ID_STATUS_DEMAND = 2;
    const ID_STATUS_INVESTIGATION = 3;
    const ID_STATUS_DEMAND_SEND_PROOF = 4;
    const ID_STATUS_NORMAL_SEND_PROOF = 5;
    const ID_STATUS_PROOVED = 6;
    const ID_STATUS_NOT_PROOVED = 7;


    /**
     * @ORM\Column(name="idStatus", type="integer")
     * @ORM\Id
     */
    private $idStatus;

    /**
     * @var string
     *
     * @Assert\Length(max="50")
     * @ORM\Column(name="libStatus", type="string", length=50, nullable=false)
     */
    private $libStatus;

    /**
     * @var integer
     *
     * @ORM\Column(name="boolRanking", type="integer", nullable=false)
     */
    private $boolRanking = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="boolSendProof", type="integer", nullable=false)
     */
    private $boolSendProof = 0;

    /**
     * Set idStatus
     *
     * @param integer $idStatus
     * @return PlayerChartStatus
     */
    public function setIdStatus($idStatus)
    {
        $this->idStatus = $idStatus;
        return $this;
    }

    /**
     * Get idStatus
     *
     * @return integer
     */
    public function getIdStatus()
    {
        return $this->idStatus;
    }


    /**
     * Set libStatus
     *
     * @param string $libStatus
     * @return PlayerChartStatus
     */
    public function setLibStatus($libStatus)
    {
        $this->libStatus = $libStatus;

        return $this;
    }

    /**
     * Get libStatus
     *
     * @return string
     */
    public function getLibStatusr()
    {
        return $this->libStatus;
    }


    /**
     * Set boolRanking
     *
     * @param integer $boolRanking
     * @return PlayerChartStatus
     */
    public function setBoolRanking($boolRanking)
    {
        $this->boolRanking = $boolRanking;
        return $this;
    }

    /**
     * Get boolRanking
     *
     * @return integer
     */
    public function getBoolRanking()
    {
        return $this->boolRanking;
    }


    /**
     * Set boolSendProof
     *
     * @param integer $boolSendProof
     * @return PlayerChartStatus
     */
    public function setBoolSendProof($boolSendProof)
    {
        $this->boolSendProof = $boolSendProof;
        return $this;
    }

    /**
     * Get boolSendProof
     *
     * @return integer
     */
    public function getBoolSendProof()
    {
        return $this->boolSendProof;
    }
}
