<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
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


#[ORM\Table(name:'vgr_video')]
#[ORM\Entity(repositoryClass: VideoRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\VideoListener"])]
#[ORM\UniqueConstraint(name: "unq_video", columns: ["type", "video_external_id"])]
#[DoctrineAssert\UniqueEntity(fields: ['url'])]
#[DoctrineAssert\UniqueEntity(fields: ['type', 'video_external_id'])]
#[ApiResource(order: ['id' => 'DESC'])]
#[ApiFilter(
    SearchFilter::class,
    properties: [
        'libVideo' => 'partial',
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
class Video implements SluggableInterface
{
    use TimestampableEntity;
    use SluggableTrait;
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
    private string $type = VideoType::TYPE_YOUTUBE;

    #[Assert\NotNull(message: 'video.videoId.not_null')]
    #[ORM\Column(length: 50, nullable: true)]
    private ?string $externalVideoId = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[ORM\Column(length: 255, nullable: true)]
    private ?string $url = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 5, max: 255)]
    #[ORM\Column(length: 255, nullable: false)]
    private string $libVideo;

    #[ORM\Column(nullable: false, options: ['default' => 0])]
    private int $nbComment = 0;


    #[ORM\ManyToOne(targetEntity: Game::class)]
    #[ORM\JoinColumn(name:'game_id', referencedColumnName:'id', nullable:true, onDelete: 'SET NULL')]
    private ?Game $game;

    /**
     * @var Collection<int, VideoComment>
     */
    #[ORM\OneToMany(targetEntity: VideoComment::class, mappedBy: 'video')]
    private Collection $comments;

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

    public function getType(): VideoType
    {
        return new VideoType($this->type);
    }

    public function setVideoExternalId(string $externalVideoId): void
    {
        $this->externalVideoId = $externalVideoId;
    }


    public function getExternalVideoId(): ?string
    {
        return $this->externalVideoId;
    }

    public function setLibVideo(string $libVideo): void
    {
        $this->libVideo = $libVideo;
    }

    public function getLibVideo(): string
    {
        return $this->libVideo;
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

    public function setGame(Game $game = null): void
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

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     *
     */
    public function majTypeAndVideoId(): void
    {
        if (strpos($this->getUrl(), 'youtube')) {
            $this->setType(VideoType::TYPE_YOUTUBE);
            $explode = explode('=', $this->getUrl());
            $this->setVideoExternalId($explode[1]);
        } elseif (strpos($this->getUrl(), 'youtu.be')) {
            $this->setType(VideoType::TYPE_YOUTUBE);
            $this->setVideoExternalId(substr($this->getUrl(), strripos($this->getUrl(), '/') + 1, strlen($this->getUrl()) - 1));
        } elseif (strpos($this->getUrl(), 'twitch')) {
            $this->setType(VideoType::TYPE_TWITCH);
            $explode = explode('/', $this->getUrl());
            $this->setVideoExternalId($explode[count($explode) - 1]);
        } else {
            $this->setType(VideoType::TYPE_UNKNOWN);
        }
    }

    public function getEmbeddedUrl(): string
    {
        if ($this->getType()->getValue() == VideoType::TYPE_YOUTUBE) {
            return 'https://www.youtube.com/embed/' . $this->getExternalVideoId();
        } elseif ($this->getType()->getValue() == VideoType::TYPE_TWITCH) {
            return 'https://player.twitch.tv/?autoplay=false&video=v' . $this->getExternalVideoId(
                ) . '&parent=' . $_SERVER['SERVER_NAME'];
        } else {
            return $this->getUrl();
        }
    }

    public function getSluggableFields(): array
    {
        return ['libVideo'];
    }
}
