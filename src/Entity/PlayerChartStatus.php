<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\PlayerChartStatusRepository;

#[ORM\Table(name:'vgr_player_chart_status')]
#[ORM\Entity(repositoryClass: PlayerChartStatusRepository::class)]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get()
    ],
    normalizationContext: ['groups' => ['player-chart-status:read']]
)]
class PlayerChartStatus implements TranslatableInterface
{
    use TranslatablePropertiesTrait;
    use TranslatableMethodsTrait;

    public const ID_STATUS_NORMAL = 1;
    public const ID_STATUS_DEMAND = 2;
    public const ID_STATUS_INVESTIGATION = 3;
    public const ID_STATUS_DEMAND_SEND_PROOF = 4;
    public const ID_STATUS_NORMAL_SEND_PROOF = 5;
    public const ID_STATUS_PROOVED = 6;
    public const ID_STATUS_NOT_PROOVED = 7;


    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 30)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $class;

    #[ORM\Column(nullable: false, options: ['default' => true])]
    private bool $boolRanking = true;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolSendProof = false;

    #[ORM\Column(type: 'smallint', nullable: false, options: ['default' => 0])]
    private int $sOrder = 0;

    /**
     * @var Collection<int, PlayerChart>
     */
    #[ORM\OneToMany(targetEntity: PlayerChart::class, mappedBy: 'status')]
    private Collection $playerCharts;

    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    public function getDefaultName(): string
    {
        return $this->translate('en', false)->getName();
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setLabel(string $class): void
    {
        $this->class = $class;
    }

    public function setClass(string $class): void
    {
        $this->class = $class;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function setBoolRanking(bool $boolRanking): void
    {
        $this->boolRanking = $boolRanking;
    }

    public function getBoolRanking(): bool
    {
        return $this->boolRanking;
    }

    public function setBoolSendProof(bool $boolSendProof): void
    {
        $this->boolSendProof = $boolSendProof;
    }

    public function getBoolSendProof(): bool
    {
        return $this->boolSendProof;
    }

    public function setName(string $name): void
    {
        $this->translate(null, false)->setName($name);
    }

    public function getName(): string
    {
        return $this->translate(null, false)->getName();
    }

    public function setSOrder(int $sOrder): void
    {
        $this->sOrder = $sOrder;
    }

    public function getSOrder(): int
    {
        return $this->sOrder;
    }

    public static function getStatusForProving(): array
    {
        return array(
            self::ID_STATUS_NORMAL,
            self::ID_STATUS_INVESTIGATION,
            self::ID_STATUS_NOT_PROOVED,
        );
    }
}
