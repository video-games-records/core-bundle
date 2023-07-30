<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait ThumbnailTrait
{
    /**
     * @ORM\Column(name="thumbnail", type="string", length=255, nullable=true)
     */
    private ?string $thumbnail;

    /**
     * Set thumbnail
     * @param string|null $thumbnail
     * @return $this
     */
    public function setThumbnail(?string $thumbnail): static
    {
        $this->thumbnail = $thumbnail;

        return $this;
    }

    /**
     * Get thumbnail
     * @return string|null
     */
    public function getThumbnail(): ?string
    {
        return $this->thumbnail;
    }
}
