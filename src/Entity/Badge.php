<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Badge
 * @ORM\Table(
 *     name="vgr_badge",
 *     indexes={
 *         @ORM\Index(name="idx_type", columns={"type"}),
 *         @ORM\Index(name="idx_value", columns={"value"})
 *     }
 * )
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\BadgeRepository")
 * @ApiResource(attributes={"order"={"type", "value"}})
 */
class Badge
{
    const TYPE_CONNEXION = 'Connexion';
    const TYPE_DON = 'Don';
    const TYPE_FORUM = 'Forum';
    const TYPE_INSCRIPTION = 'Inscription';
    const TYPE_MASTER = 'Master';
    const TYPE_PLATFORM = 'Platform';
    const TYPE_SPECIAL_WEBMASTER = 'SpecialWebmaster';
    const TYPE_VGR_CHART = 'VgrChart';
    const TYPE_VGR_PROOF = 'VgrProof';
    const TYPE_VGR_SPECIAL_COUNTRY = 'VgrSpecialCountry';
    const TYPE_VGR_SPECIAL_CUP = 'VgrSpecialCup';
    const TYPE_VGR_SPECIAL_LEGEND = 'VgrSpecialLegend';
    const TYPE_VGR_SPECIAL_MEDALS = 'VgrSpecialMedals';
    const TYPE_VGR_SPECIAL_POINTS = 'VgrSpecialPoints';
    const TYPE_TWITCH = 'Twitch';


    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\Length(max="50")
     * @ORM\Column(name="type", type="string", length=50, nullable=false)
     */
    private string $type;

    /**
     * @Assert\Length(max="100")
     * @ORM\Column(name="picture", type="string", length=100, nullable=false, options={"default" : "default.gif"})
     */
    private string $picture;

    /**
     * @ORM\Column(name="value", type="integer", nullable=false, options={"default":0})
     */
    private ?int $value = null;

    /**
     * @ORM\Column(name="nbPlayer", type="integer", nullable=false, options={"default":0})
     */
    private int $nbPlayer = 0;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", mappedBy="badge")
     */
    private ?Game $game;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Country", mappedBy="badge")
     */
    private ?Country $country;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform", mappedBy="badge")
     */
    private ?Platform $platform;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s / %s [%s]', $this->getType(), $this->getPicture(), $this->getId());
    }


    /**
     * Set id
     * @param integer $id
     * @return Badge
     */
    public function setId(int $id): Badge
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set type
     * @param string $type
     * @return Badge
     */
    public function setType(string $type): Badge
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * Set picture
     * @param string $picture
     * @return Badge
     */
    public function setPicture(string $picture): Badge
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     * @return string
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * Set value
     * @param integer $value
     * @return Badge
     */
    public function setValue(int $value): Badge
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     * @return integer
     */
    public function getValue(): ?int
    {
        return $this->value;
    }

    /**
     * Set nbPlayer
     * @param integer $nbPlayer
     * @return Badge
     */
    public function setNbPlayer(int $nbPlayer): Badge
    {
        $this->nbPlayer = $nbPlayer;

        return $this;
    }

    /**
     * Get nbPlayer
     * @return integer
     */
    public function getNbPlayer(): int
    {
        return $this->nbPlayer;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame(): ?Game
    {
        return $this->game;
    }

    /**
     * Get country
     * @return Country
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * Get platform
     * @return Platform
     */
    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    /**
     * @return string
     */
    public function getTitle(): ?string
    {
        switch ($this->getType()) {
            case self::TYPE_PLATFORM:
                return $this->getPlatform()
                    ->getLibPlatform();
            case self::TYPE_MASTER:
                return $this->getGame()
                    ->getName();
            case self::TYPE_VGR_SPECIAL_COUNTRY:
                return $this->getCountry()
                    ->getName();
            case self::TYPE_VGR_SPECIAL_CUP:
            case self::TYPE_VGR_SPECIAL_MEDALS:
            case self::TYPE_VGR_SPECIAL_LEGEND:
            case self::TYPE_VGR_SPECIAL_POINTS:
                return $this->getType() . ' ' . $this->getValue();
            case self::TYPE_FORUM:
            case self::TYPE_CONNEXION:
            case self::TYPE_DON:
            case self::TYPE_TWITCH:
            case self::TYPE_VGR_CHART:
            case self::TYPE_VGR_PROOF:
                return $this->getValue() . ' ' . $this->getType();
            default:
                return $this->getType();
        }
    }

    /**
     * @return array
     */
    public static function getTypeChoices(): array
    {
        return [
            self::TYPE_CONNEXION => self::TYPE_CONNEXION,
            self::TYPE_DON => self::TYPE_DON,
            self::TYPE_FORUM => self::TYPE_FORUM,
            self::TYPE_INSCRIPTION => self::TYPE_INSCRIPTION,
            self::TYPE_MASTER => self::TYPE_MASTER,
            self::TYPE_PLATFORM => self::TYPE_PLATFORM,
            self::TYPE_SPECIAL_WEBMASTER => self::TYPE_SPECIAL_WEBMASTER,
            self::TYPE_TWITCH => self::TYPE_TWITCH,
            self::TYPE_VGR_CHART => self::TYPE_VGR_CHART,
            self::TYPE_VGR_PROOF => self::TYPE_VGR_PROOF,
            self::TYPE_VGR_SPECIAL_COUNTRY => self::TYPE_VGR_SPECIAL_COUNTRY,
            self::TYPE_VGR_SPECIAL_CUP => self::TYPE_VGR_SPECIAL_CUP,
            self::TYPE_VGR_SPECIAL_LEGEND => self::TYPE_VGR_SPECIAL_LEGEND,
            self::TYPE_VGR_SPECIAL_MEDALS => self::TYPE_VGR_SPECIAL_MEDALS,
            self::TYPE_VGR_SPECIAL_POINTS => self::TYPE_VGR_SPECIAL_POINTS,
        ];
    }

    public function majValue(): void
    {
        if (self::TYPE_MASTER !== $this->type) {
            return;
        }
        if (0 === $this->nbPlayer) {
            $this->value = 0;
        } else {
            $this->value = floor(
                100 * (6250 * (-1 / (100 + $this->getGame()
                                ->getNbPlayer() - $this->nbPlayer) + 0.0102) / (pow($this->nbPlayer, 1 / 3)))
            );
        }
    }
}
