<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\VideoRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\DescriptionTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\IsActiveTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\LikeCountTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ThumbnailTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\TitleTrait;
use VideoGamesRecords\CoreBundle\Traits\Entity\ViewCountTrait;
use VideoGamesRecords\CoreBundle\ValueObject\VideoType;
use VideoGamesRecords\CoreBundle\Filter\VideoSearchFilter;

#[ORM\Table(name:'vgr_video')]
#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\VideoListener"])]
#[ORM\UniqueConstraint(name: "unq_video", columns: ["type", "external_id"])]
#[DoctrineAssert\UniqueEntity(fields: ['url'])]
#[DoctrineAssert\UniqueEntity(fields: ['type', 'externalId'])]
#[ApiResource(
    order: ['id' => 'DESC'],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['video:insert']],
            security: 'is_granted("ROLE_PLAYER")'
        ),
        new Put(
            denormalizationContext: ['groups' => ['video:update']],
            security: 'is_granted("ROLE_PLAYER")'
        )
    ],
    normalizationContext: ['groups' => [
        'video:read',
        'video:player', 'player:read',
        'video:game', 'game:read']
    ]
)]
#[ApiResource(
    uriTemplate: '/players/{id}/videos',
    uriVariables: [
        'id' => new Link(fromClass: Player::class, toProperty: 'player'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['video:read', 'video:game', 'game:read']],
)]
#[ApiResource(
    uriTemplate: '/games/{id}/videos',
    uriVariables: [
        'id' => new Link(fromClass: Game::class, toProperty: 'game'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['video:read', 'video:player', 'player:read:mini']],
)]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'game' => 'exact',
        'type' => 'exact',
        'player' => 'exact',
        'isActive' => 'exact',
        'title' => 'partial',
    ]
)]
#[ApiFilter(
    OrderFilter::class,
    properties: [
        'id' => 'DESC',
    ]
)]
#[ApiFilter(BooleanFilter::class, properties: ['isActive'])]
#[ApiFilter(VideoSearchFilter::class)]
class Video
{
    use TimestampableEntity;
    use PlayerTrait;
    use ViewCountTrait;
    use LikeCountTrait;
    use TitleTrait;
    use DescriptionTrait;
    use ThumbnailTrait;
    use IsActiveTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: false)]
    private string $type = VideoType::YOUTUBE;

    #[ORM\Column(length: 50, nullable: true)]
    private ?string $externalId = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;


    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbComment = 0;

    #[ORM\Column(length: 255)]
    #[Gedmo\Slug(fields: ['title'])]
    protected string $slug;


    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Game $game = null;

    /**
     * @var Collection<int, VideoComment>
     */
    #[ORM\OneToMany(targetEntity: VideoComment::class, mappedBy: 'video')]
    private Collection $comments;

    /**
     * @var Collection<int, Tag>
     */
    #[ORM\ManyToMany(targetEntity: Tag::class)]
    #[ORM\JoinTable(name: 'vgr_video_tag')]
    private Collection $tags;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->tags = new ArrayCollection();
    }

    public function __toString()
    {
        return sprintf('Video [%s]', $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getVideoType(): VideoType
    {
        return new VideoType($this->type);
    }

    public function setExternalId(string $externalId): void
    {
        $this->externalId = $externalId;
    }


    public function getExternalId(): ?string
    {
        return $this->externalId;
    }


    public function setUrl(?string $url): void
    {
        $this->url = $url;
        $this->majTypeAndVideoId();
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setGame(?Game $game = null): void
    {
        $this->game = $game;
    }

    public function setNbComment(int $nbComment): void
    {
        $this->nbComment = $nbComment;
    }

    public function getNbComment(): int
    {
        return $this->nbComment;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function getTags(): Collection
    {
        return $this->tags;
    }

    public function addTag(Tag $tag): self
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }

    public function removeTag(Tag $tag): self
    {
        $this->tags->removeElement($tag);
        return $this;
    }

    /**
     *
     */
    public function majTypeAndVideoId(): void
    {
        if (strpos($this->getUrl(), 'youtube')) {
            $this->setType(VideoType::YOUTUBE);
            $explode = explode('=', $this->getUrl());
            $this->setExternalId($explode[1]);
        } elseif (strpos($this->getUrl(), 'youtu.be')) {
            $this->setType(VideoType::YOUTUBE);
            $this->setExternalId(substr($this->getUrl(), strripos($this->getUrl(), '/') + 1, strlen($this->getUrl()) - 1));
        } elseif (strpos($this->getUrl(), 'twitch')) {
            $this->setType(VideoType::TWITCH);
            $explode = explode('/', $this->getUrl());
            $this->setExternalId($explode[count($explode) - 1]);
        } else {
            $this->setType(VideoType::UNKNOWN);
        }
    }

    public function getEmbeddedUrl(): string
    {
        if ($this->getVideoType()->getValue() == VideoType::YOUTUBE) {
            return 'https://www.youtube.com/embed/' . $this->getExternalid();
        } elseif ($this->getVideoType()->getValue() == VideoType::TWITCH) {
            return 'https://player.twitch.tv/?autoplay=false&video=v' . $this->getExternalId(
            ) . '&parent=' . $_SERVER['SERVER_NAME'];
        } else {
            return $this->getUrl();
        }
    }

    public function getSluggableFields(): array
    {
        return ['title'];
    }
}
