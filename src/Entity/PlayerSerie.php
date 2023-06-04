<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartProvenWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbChartWithoutDlcTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbGameTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankMedalTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\RankPointChartTrait;

/**
 * PlayerSerie
 *
 * @ORM\Table(name="vgr_player_serie")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlayerSerieRepository")
 */
class PlayerSerie
{
    use RankMedalTrait;
    use RankPointChartTrait;
    use NbChartTrait;
    use NbChartWithoutDlcTrait;
    use NbChartProvenTrait;
    use NbChartProvenWithoutDlcTrait;
    use NbGameTrait;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Player $player;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idSerie", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Serie $serie;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointChartWithoutDlc", type="integer", nullable=false)
     */
    private $pointChartWithoutDlc;

    /**
     * @var integer
     *
     * @ORM\Column(name="pointGame", type="integer", nullable=false)
     */
    private $pointGame;


    /**
     * Set pointChartWithoutDlc
     * @param integer $pointChartWithoutDlc
     * @return PlayerSerie
     */
    public function setPointChartWithoutDlc(int $pointChartWithoutDlc)
    {
        $this->pointChartWithoutDlc = $pointChartWithoutDlc;
        return $this;
    }

    /**
     * Get pointChartWithoutDlc
     *
     * @return integer
     */
    public function getPointChartWithoutDlc()
    {
        return $this->pointChartWithoutDlc;
    }

    /**
     * Set pointGame
     * @param integer $pointGame
     * @return PlayerSerie
     */
    public function setPointGame(int $pointGame)
    {
        $this->pointGame = $pointGame;
        return $this;
    }

    /**
     * Get pointGame
     *
     * @return integer
     */
    public function getPointGame()
    {
        return $this->pointGame;
    }


    /**
     * Set serie
     * @param Serie|null $serie
     * @return PlayerSerie
     */
    public function setSerie(Serie $serie = null)
    {
        $this->serie = $serie;

        return $this;
    }

    /**
     * Get serie
     *
     * @return Serie
     */
    public function getSerie()
    {
        return $this->serie;
    }


    /**
     * Set player
     * @param Player|null $player
     * @return PlayerSerie
     */
    public function setPlayer(Player $player = null)
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     *
     * @return Player
     */
    public function getPlayer()
    {
        return $this->player;
    }

    /**
     * @return string
     */
    public function getMedalsBackgroundColor()
    {
        $class = [
            0 => '',
            1 => 'bg-first',
            2 => 'bg-second',
            3 => 'bg-third',
        ];

        if ($this->getRankMedal() <= 3) {
            return sprintf('class="%s"', $class[$this->getRankMedal()]);
        }

        return '';
    }
}
