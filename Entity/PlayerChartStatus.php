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
    const ID_STATUS_PROOVED = 6;


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



}
