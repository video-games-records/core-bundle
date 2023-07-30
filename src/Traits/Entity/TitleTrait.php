<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait TitleTrait
{
    /**
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private ?string $title;

    /**
     * Set title
     * @param string|null $title
     * @return $this
     */
    public function setTitle(?string $title): static
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }
}
