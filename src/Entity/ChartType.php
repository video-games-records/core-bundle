<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Tools\Score;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_charttype")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartTypeRepository")
 */
class ChartType
{
    /**
     * @ORM\Column(name="idType", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $idType = null;

    /**
     * @Assert\Length(max="100")
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private ?string $name;

    /**
     * @Assert\Length(max="100")
     * @ORM\Column(name="mask", type="string", length=100)
     */
    private string $mask = '';

    /**
     * @Assert\Length(max="10")
     * @ORM\Column(name="orderBy", type="string", length=10, nullable=false)
     */
    private string $orderBy = 'ASC';


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s] %s (%s)', $this->name, $this->mask, $this->orderBy, $this->idType);
    }

    /**
     * Set mask
     *
     * @param string $mask
     * @return ChartType
     */
    public function setMask(string $mask): Self
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * Get mask
     *
     * @return string
     */
    public function getMask(): string
    {
        return $this->mask;
    }


    /**
     * Set orderBy
     *
     * @param string $orderBy
     * @return ChartType
     */
    public function setOrderBy(string $orderBy): Self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Get orderBy
     *
     * @return string
     */
    public function getOrderBy(): string
    {
        return $this->orderBy;
    }


    /**
     * Get the number of input for the mask.
     * @return int
     */
    public function getNbInput(): int
    {
        return count(explode('|', $this->getMask()));
    }

    /**
     * @return int
     */
    public function getIdType(): ?int
    {
        return $this->idType;
    }

    /**
     * @param int $idType
     * @return ChartType
     */
    public function setIdType(int $idType): Self
    {
        $this->idType = $idType;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ChartType
     */
    public function setName(string $name): Self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get parseMask
     *
     * @return array
     */
    public function getParseMask(): array
    {
        return Score::parseChartMask($this->mask);
    }
}
