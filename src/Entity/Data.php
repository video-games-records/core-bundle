<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Data
 *
 * @ORM\Table(name="vgr_data")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\DataRepository")
 * @ApiResource(attributes={"order"={"id"}})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "category": "exact",
 *          "label": "exact",
 *          "version": "exact",
*      }
 * )
 */
class Data
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\Length(max="50")
     * @ORM\Column(name="category", type="string", length=50, nullable=false)
     */
    private string $category;

    /**
     * @Assert\Length(max="50")
     * @ORM\Column(name="label", type="string", length=50, nullable=false)
     */
    private string $label;

    /**
     * @Assert\Length(max="50")
     * @ORM\Column(name="value", type="string", length=50, nullable=false)
     */
    private string $value;

    /**
     * @Assert\Length(max="10")
     * @ORM\Column(name="version", type="string", length=50, nullable=false)
     */
    private string $version;

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getLabel(), $this->getId());
    }


    /**
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): Self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param string $category
     * @return $this
     */
    public function setCategory(string $category): Self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }


    /**
     * @param string $label
     * @return $this
     */
    public function setLabel(string $label): Self
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }


    /**
     * @param string $value
     * @return $this
     */
    public function setValue(string $value): Self
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $version
     * @return $this
     */
    public function setVersion(string $version): Self
    {
        $this->version = $version;

        return $this;
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return $this->version;
    }
}
