<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chart
 *
 * @ORM\Table(name="vgr_librecord_type", indexes={@ORM\Index(name="idxIdType", columns={"idType"}) })
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
     * @ORM\Column(name="lib_fr", type="string", length=50, nullable=true)
     */
    private $lib_fr;

    /**
     * @var string
     *
     * @ORM\Column(name="lib_en", type="string", length=50, nullable=true)
     */
    private $lib_en;

    /**
     * @var string
     *
     * @ORM\Column(name="nomType", type="string", length=100, nullable=true)
     */
    private $nomType;

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



}