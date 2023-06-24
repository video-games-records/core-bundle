<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Player;

use Doctrine\ORM\Mapping as ORM;

trait PlayerCommunicationDataTrait
{
    /**
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     */
    protected ?string $website;

    /**
     * @ORM\Column(name="youtube", type="string", length=255, nullable=true)
     */
    protected ?string $youtube;

    /**
     * @ORM\Column(name="twitch", type="string", length=255, nullable=true)
     */
    protected ?string $twitch;

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    /**
     * @param string|null $website
     * @return $this
     */
    public function setWebsite(string $website = null): static
    {
        $this->website = $website;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    /**
     * @param string|null $youtube
     * @return $this
     */
    public function setYoutube(string $youtube = null): static
    {
        $this->youtube = $youtube;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    /**
     * @param string|null $twitch
     * @return $this
     */
    public function setTwitch(string $twitch = null): static
    {
        $this->twitch = $twitch;
        return $this;
    }
}
