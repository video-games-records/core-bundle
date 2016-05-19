<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserChartLib
 *
 * @ORM\Table(name="vgr_librecord_membre", indexes={@ORM\Index(name="idxIdLibRecord", columns={"idLibRecord"}), @ORM\Index(name="idxIdMembre", columns={"idMembre"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\UserChartLibRepository")
 */
class UserChartLib
{

    /**
     * @ORM\Column(name="idMembre", type="integer")
     * @ORM\Id
     */
    private $idMembre;

    /**
     * @ORM\Column(name="idLibRecord", type="integer")
     * @ORM\Id
     */
    private $idLibRecord;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", nullable=false)
     */
    private $value;

}