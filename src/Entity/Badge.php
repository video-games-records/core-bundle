<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPlayerTrait;

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
class Badge implements BadgeInterface
{
    use NbPlayerTrait;

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
    private int $value = 0;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", mappedBy="badge")
     */
    private ?Game $game;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Serie", mappedBy="badge")
     */
    private ?Serie $serie;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Country", mappedBy="badge")
     */
    private ?Country $country;

    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Platform", mappedBy="badge")
     */
    private ?Platform $platform;

    private string $title = '';

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
     * @return string|null
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
     * @return int
     */
    public function getValue(): int
    {
        return $this->value;
    }

    /**
     * Get game
     * @return Game|null
     */
    public function getGame(): ?Game
    {
        return $this->game;
    }

    /**
     * @return Serie|null
     */
    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    /**
     * Get country
     * @return Country|null
     */
    public function getCountry(): ?Country
    {
        return $this->country;
    }

    /**
     * Get platform
     * @return Platform|null
     */
    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }


    /**
     * @return array
     */
    public static function getTypeChoices(): array
    {
        return [
            self::TYPE_CONNEXION                => self::TYPE_CONNEXION,
            self::TYPE_DON                      => self::TYPE_DON,
            self::TYPE_FORUM                    => self::TYPE_FORUM,
            self::TYPE_INSCRIPTION              => self::TYPE_INSCRIPTION,
            self::TYPE_MASTER                   => self::TYPE_MASTER,
            self::TYPE_PLATFORM                 => self::TYPE_PLATFORM,
            self::TYPE_SERIE                    => self::TYPE_SERIE,
            self::TYPE_SPECIAL_WEBMASTER        => self::TYPE_SPECIAL_WEBMASTER,
            self::TYPE_TWITCH                   => self::TYPE_TWITCH,
            self::TYPE_VGR_CHART                => self::TYPE_VGR_CHART,
            self::TYPE_VGR_PROOF                => self::TYPE_VGR_PROOF,
            self::TYPE_VGR_SPECIAL_COUNTRY      => self::TYPE_VGR_SPECIAL_COUNTRY,
            self::TYPE_VGR_SPECIAL_CUP          => self::TYPE_VGR_SPECIAL_CUP,
            self::TYPE_VGR_SPECIAL_LEGEND       => self::TYPE_VGR_SPECIAL_LEGEND,
            self::TYPE_VGR_SPECIAL_MEDALS       => self::TYPE_VGR_SPECIAL_MEDALS,
            self::TYPE_VGR_SPECIAL_POINTS       => self::TYPE_VGR_SPECIAL_POINTS,
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
            $this->value = (int) floor(
                100 * (6250 * (-1 / (100 + $this->getGame()
                                ->getNbPlayer() - $this->nbPlayer) + 0.0102) / (pow($this->nbPlayer, 1 / 3)))
            );
        }
    }

    /**
     * @return bool
     */
    public function isTypeMaster(): bool
    {
        return $this->type === self::TYPE_MASTER;
    }
}
