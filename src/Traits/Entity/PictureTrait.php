<?php

namespace VideoGamesRecords\CoreBundle\Traits\Entity;

trait PictureTrait
{
    /**
     * @Assert\Length(max="200")
     * @ORM\Column(name="picture", type="string", length=200, nullable=true)
     */
    private ?string $picture;

    /**
     * @param string|null $picture
     */
    public function setPicture(string $picture = null): void
    {
        $this->picture = $picture;
    }

    /**
     * @return string|null
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }
}
