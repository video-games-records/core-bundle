<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;
use DateTime;

/**
 * GameDay
 *
 * @ORM\Table(name="vgr_game_day")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameDayRepository")
 * @UniqueEntity("day")
 */

class GameDay
{
    /**
     * @var integer
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @Assert\NotNull
     * @ORM\Column(name="day", type="date", nullable=true)
     */
    private $day;

    /**
     * @var Game
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="days")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;


    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s [%s]', $this->getDay()->format('Y-m-d'), $this->id);
    }

    /**
     * Set idGroup
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): GameDay
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get idGroup
     * @return integer
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set day
     * @param DateTime $day
     * @return $this
     */
    public function setDay(\DateTime $day): GameDay
    {
        $this->day = $day;
        return $this;
    }

    /**
     * Get day
     *
     * @return DateTime
     */
    public function getDay(): ?DateTime
    {
        return $this->day;
    }

    /**
     * Set Game
     * @param Game $game
     * @return $this
     */
    public function setGame(Game $game): GameDay
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame(): ?Game
    {
        return $this->game;
    }
}
