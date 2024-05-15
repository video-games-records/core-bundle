<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Traits\Entity\Player;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait PlayerCommunicationDataTrait
{
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $website;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $youtube;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $twitch;

    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $discord;

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(string $website = null): void
    {
        $this->website = $website;
    }

    public function getYoutube(): ?string
    {
        return $this->youtube;
    }

    public function setYoutube(string $youtube = null): void
    {
        $this->youtube = $youtube;
    }

    public function getTwitch(): ?string
    {
        return $this->twitch;
    }

    public function setTwitch(string $twitch = null): void
    {
        $this->twitch = $twitch;
    }

    public function getDiscord(): ?string
    {
        return $this->discord;
    }

    public function setDiscord(?string $discord): void
    {
        $this->discord = $discord;
    }
}
