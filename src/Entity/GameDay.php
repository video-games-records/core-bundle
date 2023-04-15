<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * GameDay
 *
 * @ORM\Table(name="vgr_game_day")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameDayRepository")
 * @UniqueEntity("day")
 * @ApiFilter(DateFilter::class, properties={"day": DateFilter::EXCLUDE_NULL})
 */

class GameDay
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     *
     * @Assert\NotNull
     * @ORM\Column(name="day", type="date", nullable=false)
     */
    private DateTime $day;

    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="days")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Game $game;


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
     * @param integer $id
     * @return GameDay
     */
    public function setId(int $id): GameDay
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
     * Set day
     * @param $day
     * @return GameDay
     */
    public function setDay($day): GameDay
    {
        $this->day = $day;
        return $this;
    }

    /**
     * Get day
     *
     * @return DateTime
     */
    public function getDay(): DateTime
    {
        return $this->day;
    }

    /**
     * Set Game
     * @param Game $game
     * @return GameDay
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
    public function getGame(): Game
    {
        return $this->game;
    }
}
