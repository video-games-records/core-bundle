<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
     * @ORM\Column(name="libFr", type="string", length=50, nullable=true)
     */
    private $libFr;

    /**
     * @var string
     *
     * @ORM\Column(name="libEn", type="string", length=50, nullable=true)
     */
    private $libEn;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="mask", type="string", length=100, nullable=true)
     */
    private $mask;

    /**
     * @var string
     *
     * @ORM\Column(name="orderBy", type="string", length=10, nullable=true)
     */
    private $orderBy;

    /**
     * Get lib
     *
     * @return string
     */
    public function getLib()
    {
        return $this->libEn;
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
}
