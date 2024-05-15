<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Metadata\ApiFilter;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Contracts\BadgeInterface;
use VideoGamesRecords\CoreBundle\Repository\BadgeRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\NbPlayerTrait;

#[ORM\Table(name:'vgr_badge')]
#[ORM\Entity(repositoryClass: BadgeRepository::class)]
#[ORM\Index(name: "idx_type", columns: ["type"])]
#[ORM\Index(name: "idx_value", columns: ["value"])]
#[ApiFilter(OrderFilter::class, properties: ['type', 'value'], arguments: ['orderParameterName' => 'order'])]
class Badge implements BadgeInterface
{
    use NbPlayerTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: false)]
    private string $type;

    #[Assert\Length(max: 100)]
    #[ORM\Column(length: 100, nullable: false, options: ['default' => 'default.gif'])]
    private string $picture;

    #[ORM\Column(length: 100, nullable: false, options: ['default' => 0])]
    private int $value = 0;

    #[ORM\OneToOne(targetEntity: "Game", mappedBy: "badge")]
    private ?Game $game;

    #[ORM\OneToOne(targetEntity: "Serie", mappedBy: "badge")]
    private ?Serie $serie;

    #[ORM\OneToOne(targetEntity: "Country", mappedBy: "badge")]
    private ?Country $country;

    #[ORM\OneToOne(targetEntity: "Platform", mappedBy: "badge")]
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
     * @param integer $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }


    /**
     * @param string $picture
     */
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
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
     * @param integer $value
     */
    public function setValue(int $value): void
    {
        $this->value = $value;
    }

    /**
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

    /**
     * @param string $title
     * @return void
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
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
