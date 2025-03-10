<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\OpenApi\Model;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableMethodsTrait;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatablePropertiesTrait;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Controller\Country\GetRanking;
use VideoGamesRecords\CoreBundle\Repository\CountryRepository;

#[ORM\Table(name:'vgr_country')]
#[ORM\Entity(repositoryClass: CountryRepository::class)]
#[ApiResource(
    order: ['translations.name' => 'ASC'],
    operations: [
        new GetCollection(),
        new Get(),
        new Get(
            uriTemplate: '/countries/{id}/ranking',
            controller: GetRanking::class,
            normalizationContext: ['groups' => [
                'player:read', 'team:read']
            ],
            openapi: new Model\Operation(
                summary: 'Retrieves the country leaderboard',
                description: 'Retrieves the country leaderboard'
            ),
            /*openapiContext: [
                'parameters' => [
                    [
                        'name' => 'maxRank',
                        'in' => 'query',
                        'type' => 'integer',
                        'required' => false
                    ],
                ]
            ]*/
        ),
    ],
    normalizationContext: ['groups' => ['country:read']]
)]
class Country implements TranslatableInterface
{
    use TranslatablePropertiesTrait;
    use TranslatableMethodsTrait;

    #[Assert\Length(max: 2)]
    #[ORM\Column(length: 2, nullable: false)]
    private string $codeIso2;

    #[Assert\Length(max: 3)]
    #[ORM\Column(length: 3, nullable: false)]
    private string $codeIso3;

    #[ORM\Column(nullable: false)]
    private int $codeIsoNumeric;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 255, nullable: true)]
    private string $slug;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\OneToOne(targetEntity: Badge::class, cascade: ['persist'], inversedBy: 'country')]
    #[ORM\JoinColumn(name:'badge_id', referencedColumnName:'id', nullable:true)]
    private ?Badge $badge;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $boolMaj = false;

    public function setCodeIso2(string $codeIso2): void
    {
        $this->codeIso2 = $codeIso2;
    }

    public function getCodeIso2(): string
    {
        return $this->codeIso2;
    }

    public function getCodeIso3(): string
    {
        return $this->codeIso3;
    }

    public function setCodeIso3(string $codeIso3): void
    {
        $this->codeIso3 = $codeIso3;
    }

    public function getCodeIsoNumeric(): int
    {
        return $this->codeIsoNumeric;
    }

    public function setCodeIsoNumeric(int $codeIsoNumeric): void
    {
        $this->codeIsoNumeric = $codeIsoNumeric;
    }

    public function setName(string $name): void
    {
        $this->translate(null, false)->setName($name);
    }

    public function getName(): string
    {
        return $this->translate(null, false)->getName();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }


    public function setBadge(Badge $badge = null): void
    {
        $this->badge = $badge;
    }

    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    public function setBoolMaj(bool $boolMaj): void
    {
        $this->boolMaj = $boolMaj;
    }

    public function getBoolMaj(): bool
    {
        return $this->boolMaj;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
    }

    public function __toString()
    {
        return sprintf('%s [%d]', $this->getDefaultName(), $this->getId());
    }

    public function getDefaultName(): string
    {
        return $this->translate('en', false)->getName();
    }
}
