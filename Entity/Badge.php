<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * Badge
 *
 * @ORM\Table(name="vgr_badge")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\BadgeRepository")
 * @ApiResource(attributes={"order"={"type", "value"}})
 */
class Badge
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\Length(max="50")
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @Assert\Length(max="100")
     * @ORM\Column(name="picture", type="string", length=50, nullable=false)
     */
    private $picture;

    /**
     * @var integer
     *
     * @ORM\Column(name="value", type="integer", nullable=true, options={"default":0})
     */
    private $value = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbPlayer", type="integer", nullable=true, options={"default":0})
     */
    private $nbPlayer = 0;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", mappedBy="badge")
     */
    private $game;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Country", mappedBy="badge")
     */
    private $country;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s / %s [%s]', $this->getType(), $this->getPicture(), $this->getId());
    }


    /**
     * Set id
     *
     * @param integer $id
     * @return Badge
     */
    public function setId(int $id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set type
     *
     * @param string $type
     * @return Badge
     */
    public function setType(string $type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }


    /**
     * Set picture
     *
     * @param string $picture
     * @return Badge
     */
    public function setPicture(string $picture)
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
     * Set value
     *
     * @param integer $value
     * @return Badge
     */
    public function setValue(int $value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return integer
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Set nbPlayer
     *
     * @param integer $nbPlayer
     * @return $this
     */
    public function setNbPlayer(int $nbPlayer)
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     *
     * @return integer
     */
    public function getNbPlayer()
    {
        return $this->nbPlayer;
    }

    /**
     * Get game
     *
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * Get country
     *
     * @return Country
     */
    public function getCountry()
    {
        return $this->country;
    }
}
