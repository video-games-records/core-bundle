<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * PlayerStatus
 *
 * @ORM\Table(name="player_status")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerStatusRepository")
 */
class PlayerStatus implements TranslatableInterface
{
    use TranslatableTrait;


    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     */
    private ?int $id = null;

    /**
     * @Assert\Length(max="30")
     * @ORM\Column(name="class", type="string", length=30, nullable=false)
     */
    private ?string $class = null;


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getDefaultName(), $this->id);
    }

    /**
     * @return string
     */
    public function getDefaultName(): string
    {
        return $this->translate('en', false)->getName();
    }

    /**
     * Set id
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * Set class
     * @param string $class
     * @return $this
     */
    public function setClass(string $class): self
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass(): ?string
    {
        return $this->class;
    }


    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->translate(null, false)->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->translate(null, false)->getName();
    }
}
