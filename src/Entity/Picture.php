<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use VideoGamesRecords\CoreBundle\Traits\Entity\Game\GameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerTrait;

/**
 * Proof
 *
 * @ORM\Table(name="vgr_picture")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PictureRepository")
 */
class Picture
{
    use PlayerTrait;
    use GameTrait;
    use TimestampableEntity;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="path", type="string", nullable=false)
     */
    private string $path = '';

    /**
     * @ORM\Column(name="metadata", type="text", nullable=true)
     */
    private ?string $metadata = null;

    /**
     * @var string
     * @ORM\Column(name="hash", type="string", nullable=false)
     */
    private string $hash;


    public function __construct()
    {
    }


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Picture [%s]', $this->id);
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): static
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Set path
     *
     * @param string $path
     * @return $this
     */
    public function setPath(string $path): static
    {
        $this->path = $path;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Set metadata
     *
     * @param string $metadata
     * @return $this
     */
    public function setMetadata(string $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }

    /**
     * Get metadata
     *
     * @return string
     */
    public function getMetadata(): ?string
    {
        return $this->metadata;
    }

    /**
     * Set hash
     *
     * @param string $hash
     * @return $this
     */
    public function setHash(string $hash): static
    {
        $this->hash = $hash;
        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getHash(): string
    {
        return $this->hash;
    }
}
