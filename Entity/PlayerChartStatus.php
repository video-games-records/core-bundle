<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * PlayerChartStatus
 *
 * @ORM\Table(name="vgr_player_chart_status")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository")
 */
class PlayerChartStatus implements TranslatableInterface
{
    use TranslatableTrait;

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
     * @Assert\Length(max="30")
     * @ORM\Column(name="class", type="string", length=30, nullable=false)
     */
    private $class;

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
     * @var ArrayCollection|PlayerChart[]
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart", mappedBy="status")
     */
    private $playerCharts;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return $this->translate('en', false)->getName();
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
     * Set class
     * @param string $class
     * @return $this
     */
    public function setLabel(string $class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }


    /**
     * Set boolRanking
     * @param integer $boolRanking
     * @return $this
     */
    public function setBoolRanking(int $boolRanking)
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
     * @param integer $boolSendProof
     * @return $this
     */
    public function setBoolSendProof(int $boolSendProof)
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
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->translate(null, false)->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->translate(null, false)->getName();
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
