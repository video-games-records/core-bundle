<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlayerChartStatus
 *
 * @ORM\Table(name="vgr_player_chart_status")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository")
 */
class PlayerChartStatus implements TranslatableInterface
{
    use TranslatablePropertiesTrait;
    use TranslatableMethodsTrait;

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
    private ?int $id = null;

    /**
     * @Assert\Length(max="30")
     * @ORM\Column(name="class", type="string", length=30, nullable=false)
     */
    private string $class;

    /**
     * @ORM\Column(name="boolRanking", type="boolean", nullable=false, options={"default" : true})
     */
    private bool $boolRanking = true;

    /**
     * @ORM\Column(name="boolSendProof", type="boolean", nullable=false, options={"default" : false})
     */
    private bool $boolSendProof = false;

    /**
     * @ORM\Column(name="sOrder", type="smallint", nullable=false, options={"default" : 0})
     */
    private int $sOrder = 0;

    /**
     * @var Collection<PlayerChart>
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\PlayerChart", mappedBy="status")
     */
    private Collection $playerCharts;

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
    public function getDefaultName(): string
    {
        return $this->translate('en', false)->getName();
    }

    /**
     * Set id
     * @param integer $id
     * @return PlayerChartStatus
     */
    public function setId(int $id): PlayerChartStatus
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Set class
     * @param string $class
     * @return PlayerChartStatus
     */
    public function setLabel(string $class): PlayerChartStatus
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @param string $class
     * @return PlayerChartStatus
     */
    public function setClass(string $class): PlayerChartStatus
    {
        $this->class = $class;
        return $this;
    }


    /**
     * Get class
     *
     * @return string
     */
    public function getClass(): string
    {
        return $this->class;
    }


    /**
     * Set boolRanking
     * @param bool $boolRanking
     * @return PlayerChartStatus
     */
    public function setBoolRanking(bool $boolRanking): PlayerChartStatus
    {
        $this->boolRanking = $boolRanking;
        return $this;
    }

    /**
     * Get boolRanking
     *
     * @return bool
     */
    public function getBoolRanking(): bool
    {
        return $this->boolRanking;
    }


    /**
     * Set boolSendProof
     * @param bool $boolSendProof
     * @return PlayerChartStatus
     */
    public function setBoolSendProof(bool $boolSendProof): PlayerChartStatus
    {
        $this->boolSendProof = $boolSendProof;
        return $this;
    }

    /**
     * Get boolSendProof
     *
     * @return bool
     */
    public function getBoolSendProof(): bool
    {
        return $this->boolSendProof;
    }

    /**
     * @param string $name
     * @return PlayerChartStatus
     */
    public function setName(string $name): PlayerChartStatus
    {
        $this->translate(null, false)->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->translate(null, false)->getName();
    }

    /**
     * Set sOrder
     *
     * @param integer $sOrder
     * @return PlayerChartStatus
     */
    public function setSOrder(int $sOrder): PlayerChartStatus
    {
        $this->sOrder = $sOrder;

        return $this;
    }

    /**
     * Get sOrder
     *
     * @return integer
     */
    public function getSOrder(): int
    {
        return $this->sOrder;
    }

    /**
     * @return array
     */
    public static function getStatusForProving(): array
    {
        return array(
            self::ID_STATUS_NORMAL,
            self::ID_STATUS_INVESTIGATION,
            self::ID_STATUS_NOT_PROOVED,
        );
    }
}
