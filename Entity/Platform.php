<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="vgr_platform", indexes={@ORM\Index(name="idxLibPlatform", columns={"libPlatform"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlatformRepository")
 *
 */
class Platform
{
    const NUM_ITEMS = 20;

    /**
     * @var integer
     *
     * @ORM\Column(name="idPlatform", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idPlatform;

    /**
     * @var string
     *
     * @Assert\Length(max="100")
     * @ORM\Column(name="libPlatform", type="string", length=100, nullable=true)
     */
    private $libPlatform;

    /**
     * @var string
     *
     * @Assert\Length(max="30")
     * @ORM\Column(name="picture", type="string", length=30, nullable=true)
     */
    private $picture;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = 'INACTIF';

    /**
     * @var string
     *
     * @Assert\Length(max="30")
     * @ORM\Column(name="class", type="string", length=30, nullable=true)
     */
    private $class;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->libPlatform, $this->idPlatform);
    }

    /**
     * Set idPlatform
     *
     * @param integer $idPlatform
     * @return $this
     */
    public function setIdPlatform($idPlatform)
    {
        $this->idPlatform = $idPlatform;
        return $this;
    }


    /**
     * Get idPlatform
     *
     * @return integer
     */
    public function getIdPlatform()
    {
        return $this->idPlatform;
    }

    /**
     * Get libPlatform
     *
     * @return string
     */
    public function getLibPlatform()
    {
        return $this->libPlatform;
    }

    /**
     * Set libPlaform
     *
     * @param string $libPlatform
     * @return $this
     */
    public function setLibPlatform($libPlatform)
    {
        $this->libPlatform = $libPlatform;

        return $this;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return $this
     */
    public function setPicture($picture)
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture()
    {
        return $this->picture;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set class
     *
     * @param string $class
     * @return $this
     */
    public function setClass($class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * Get class
     *
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }
}
