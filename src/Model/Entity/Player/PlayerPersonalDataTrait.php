<?php

namespace VideoGamesRecords\CoreBundle\Model\Entity\Player;

use DateTime;
use VideoGamesRecords\CoreBundle\Entity\Country;
use VideoGamesRecords\CoreBundle\Entity\Player;

trait PlayerPersonalDataTrait
{
    /**
     * @ORM\Column(name="presentation", type="text", length=65535, nullable=true)
     */
    private ?string $presentation;

    /**
     * @ORM\Column(name="collection", type="text", length=65535, nullable=true)
     */
    private ?string $collection;

    /**
     * @ORM\Column(name="birthDate", type="date", nullable=true)
     */
    protected ?DateTime $birthDate;

    /**
     * @ORM\Column(name="gender", type="string", length=1, nullable=false, options={"default" : "I"}))
     */
    protected string $gender = 'I';

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Country")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idCountry", referencedColumnName="id", nullable=true)
     * })
     */
    protected ?Country $country;

    /**
     * @ORM\Column(name="displayPersonalInfos", type="boolean", nullable=false)
     */
    private bool $displayPersonalInfos = false;


     /**
     * Set presentation
     *
     * @param string|null $presentation
     * @return $this
     */
    public function setPresentation(string $presentation = null): static
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     * @return string|null
     */
    public function getPresentation(): ?string
    {
        return $this->presentation;
    }

    /**
     * Set collection
     *
     * @param string|null $collection
     * @return Player
     */
    public function setCollection(string $collection = null): static
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     * @return string|null
     */
    public function getCollection(): ?string
    {
        return $this->collection;
    }

    /**
     * @param DateTime|null $birthDate
     * @return $this
     */
    public function setBirthDate(DateTime $birthDate = null): static
    {
        $this->birthDate = $birthDate;
        return $this;
    }


     /**
     * @return DateTime|null
     */
    public function getBirthDate(): ?DateTime
    {
        return $this->birthDate;
    }

    /**
     * @param string $gender
     * @return $this
     */
    public function setGender(string $gender): static
    {
        $this->gender = $gender;
        return $this;
    }

    /**
     * @return string
     */
    public function getGender(): string
    {
        return $this->gender;
    }



    /**
     * Set displayPersonalInfos
     * @param bool $displayPersonalInfos
     * @return $this
     */
    public function setDisplayPersonalInfos(bool $displayPersonalInfos): static
    {
        $this->displayPersonalInfos = $displayPersonalInfos;

        return $this;
    }

    /**
     * Get DisplayPersonalInfos
     * @return bool
     */
    public function getDisplayPersonalInfos(): bool
    {
        return $this->displayPersonalInfos;
    }


    /**
     * @param $country
     * @return $this
     */
    public function setCountry($country): static
    {
        $this->country = $country;
        return $this;
    }

      /**
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }



}