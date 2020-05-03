<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlayerChartStatus
 *
 * @ORM\Table(name="vgr_player_chart_status", indexes={@ORM\Index(name="idxIdStatus", columns={"id"})})
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
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\Length(max="50")
     * @ORM\Column(name="label", type="string", length=50, nullable=false)
     */
    private $label;

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
     * Set label
     *
     * @param string $label
     * @return $this
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }


    /**
     * Set boolRanking
     *
     * @param integer $boolRanking
     * @return $this
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
     * @return $this
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

    /**
     * @return array
     */
    public static function getStatusForProving()
    {
        return array(
            self::ID_STATUS_NORMAL,
            self::ID_STATUS_INVESTIGATION,
            self::ID_STATUS_NOT_PROOVED,
        );
    }
}
