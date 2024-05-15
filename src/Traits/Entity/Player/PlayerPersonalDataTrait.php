<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Player;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Entity\Country;

trait PlayerPersonalDataTrait
{
    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $presentation;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $collection;

    #[ORM\Column(type: 'date', nullable: true)]
    protected ?DateTime $birthDate;

    #[ORM\Column(nullable: false, length: 1, options: ['default' => 'I'])]
    protected string $gender = 'I';

    #[ORM\ManyToOne(targetEntity: Country::class)]
    #[ORM\JoinColumn(name:'country_id', referencedColumnName:'id', nullable:true)]
    protected ?Country $country;

    #[ORM\Column(nullable: false, options: ['default' => false])]
    private bool $displayPersonalInfos = false;


    public function setPresentation(string $presentation = null): void
    {
        $this->presentation = $presentation;
    }

    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    public function setCollection(string $collection = null): void
    {
        $this->collection = $collection;
    }

    public function getCollection(): ?string
    {
        return $this->collection;
    }

    public function setBirthDate(DateTime $birthDate = null): void
    {
        $this->birthDate = $birthDate;
    }

    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    public function setGender(string $gender): void
    {
        $this->gender = $gender;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function setDisplayPersonalInfos(bool $displayPersonalInfos): void
    {
        $this->displayPersonalInfos = $displayPersonalInfos;
    }

    public function getDisplayPersonalInfos(): bool
    {
        return $this->displayPersonalInfos;
    }

    public function setCountry($country): void
    {
        $this->country = $country;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }
}
