<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\ChartLibRepository;

#[ORM\Table(name:'vgr_chartlib')]
#[ORM\Entity(repositoryClass: ChartLibRepository::class)]
#[ApiResource(
    operations: [
        new Get()
    ],
    normalizationContext: ['groups' => ['chart-lib:read']]
)]
class ChartLib
{
    use TimestampableEntity;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: true)]
    private ?string $name;

    #[ORM\ManyToOne(targetEntity: Chart::class, inversedBy: 'libs')]
    #[ORM\JoinColumn(name:'chart_id', referencedColumnName:'id', nullable:false, onDelete:'CASCADE')]
    private Chart $chart;


    #[ORM\ManyToOne(targetEntity: ChartType::class, fetch: 'EAGER')]
    #[ORM\JoinColumn(name:'type_id', referencedColumnName:'id', nullable:false)]
    private ChartType $type;


    public function setName(string $name = null): void
    {
        $this->name = $name;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setChart(Chart $chart): void
    {
        $this->chart = $chart;
    }

    public function getChart(): Chart
    {
        return $this->chart;
    }

    public function setType(ChartType $type): void
    {
        $this->type = $type;
    }

    public function getType(): ChartType
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getType()->getName(), $this->id);
    }
}
