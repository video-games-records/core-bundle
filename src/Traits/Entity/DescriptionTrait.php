<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait DescriptionTrait
{
    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private ?string $description;

    /**
     * Set description
     * @param string|null $description
     * @return $this
     */
    public function setDescription(?string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }
}
