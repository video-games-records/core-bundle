<?php

namespace VideoGamesRecords\CoreBundle\Entity;

class Twitch
{
    /**
     * @var int|null
     */
    private ?int $id = null;

    public function __construct($id)
    {
        $this->id = $id;
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%d', $this->getId());
    }


    /**
     * Set id
     * @param integer $id
     * @return Twitch
     */
    public function setId(int $id): Self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }
}
