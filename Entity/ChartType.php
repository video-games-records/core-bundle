<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Tools\Score;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_charttype", indexes={@ORM\Index(name="idxIdType", columns={"idType"}) })
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\ChartTypeRepository")
 */
class ChartType
{
    /**
     * @var integer
     *
     * @ORM\Column(name="idType", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idType;

    /**
     * @var string
     *
     * @Assert\Length(max="100")
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @Assert\Length(max="100")
     * @ORM\Column(name="mask", type="string", length=100)
     */
    private $mask;

    /**
     * @var string
     *
     * @Assert\Length(max="10")
     * @ORM\Column(name="orderBy", type="string", length=10, nullable=true)
     */
    private $orderBy;


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
    public function setMask($mask)
    {
        $this->mask = $mask;
        return $this;
    }

    /**
     * Get mask
     *
     * @return string
     */
    public function getMask()
    {
        return $this->mask;
    }


    /**
     * Set orderBy
     *
     * @param string $orderBy
     * @return ChartType
     */
    public function setOrderBy($orderBy)
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * Get orderBy
     *
     * @return string
     */
    public function getOrderBy()
    {
        return $this->orderBy;
    }


    /**
     * Get the number of input for the mask.
     * @return int
     */
    public function getNbInput()
    {
        return count(explode('|', $this->getMask()));
    }

    /**
     * @return int
     */
    public function getIdType()
    {
        return $this->idType;
    }

    /**
     * @param int $idType
     * @return ChartType
     */
    public function setIdType($idType)
    {
        $this->idType = $idType;
        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     * @return ChartType
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get parseMask
     *
     * @return array
     */
    public function getParseMask()
    {
        return Score::parseChartMask($this->mask);
    }
}
