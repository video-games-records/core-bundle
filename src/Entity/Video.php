<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Entity\PlayerTrait;

/**
 * Video
 *
 * @ORM\Table(name="vgr_video")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\VideoRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(attributes={"order"={"id": "DESC"}})
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(BooleanFilter::class, properties={"boolActive"})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "libVideo": "partial",
 *          "game": "exact",
 *      }
 * )
 * @DoctrineAssert\UniqueEntity(fields={"url"})
 * @DoctrineAssert\UniqueEntity(fields={"type", "videoId"})
 */
class Video implements TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;
    use PlayerTrait;

    const TYPE_YOUTUBE = 'Youtube';
    const TYPE_TWITCH = 'Twitch';
    const TYPE_UNKNOWN = 'Unknown';

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @ORM\Column(name="boolActive", type="boolean", nullable=false, options={"default":true})
     */
    private bool $boolActive = true;

    /**
     * @ORM\Column(name="type", type="string", nullable=true)
     */
    private ?string $type = self::TYPE_YOUTUBE;

    /**
     * @Assert\NotNull(message="video.videoId.not_null")
     * @ORM\Column(name="videoId", type="string", nullable=true)
     */
    private ?string $videoId = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     * @ORM\Column(name="url", type="string", length=255, nullable=true)
     */
    private ?string $url = null;

    /**
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="200")
     * @ORM\Column(name="libVideo", type="string", length=200, nullable=false)
     */
    private string $libVideo;

    /**
     * @ORM\Column(name="nbComment", type="integer", nullable=false, options={"default":0})
     */
    private int $nbComment = 0;

    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="videos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=true)
     * })
     */
    private ?Game $game;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\VideoComment", mappedBy="video")
     */
    private Collection $comments;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Video [%s]', $this->id);
    }

    /**
     * Set id
     * @param integer $id
     * @return Video
     */
    public function setId(int $id): Video
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set boolActive
     * @param bool $boolActive
     * @return Video
     */
    public function setBoolActive(bool $boolActive): Video
    {
        $this->boolActive = $boolActive;

        return $this;
    }

    /**
     * Get boolActive
     *
     * @return bool
     */
    public function getBoolActive(): bool
    {
        return $this->boolActive;
    }

    /**
     * Set type
     * @param string $type
     * @return Video
     */
    public function setType(string $type): Video
    {
        $this->type = $type;
        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType(): ?string
    {
        return $this->type;
    }


    /**
     * Set videoId
     * @param string $videoId
     * @return $this
     */
    public function setVideoId(string $videoId): Video
    {
        $this->videoId = $videoId;
        return $this;
    }

    /**
     * Get videoId
     *
     * @return string
     */
    public function getVideoId(): ?string
    {
        return $this->videoId;
    }

    /**
     * Set libVideo
     * @param string $libVideo
     * @return Video
     */
    public function setLibVideo(string $libVideo): Video
    {
        $this->libVideo = $libVideo;

        return $this;
    }

    /**
     * Get libVideo
     *
     * @return string
     */
    public function getLibVideo(): string
    {
        return $this->libVideo;
    }

    /**
     * Set url
     * @param string|null $url
     * @return Video
     */
    public function setUrl(?string $url): Video
    {
        $this->url = $url;
        $this->majTypeAndVideoId();
        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Set game
     * @param Game|null $game
     * @return Video
     */
    public function setGame(Game $game = null): Video
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Set nbComment
     * @param integer $nbComment
     * @return $this
     */
    public function setNbComment(int $nbComment): Video
    {
        $this->nbComment = $nbComment;

        return $this;
    }

    /**
     * Get nbComment
     *
     * @return integer
     */
    public function getNbComment(): int
    {
        return $this->nbComment;
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
     * @return Collection
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    /**
     * @return array
     */
    public static function getTypeChoices(): array
    {
        return [
            self::TYPE_YOUTUBE => self::TYPE_YOUTUBE,
            self::TYPE_TWITCH => self::TYPE_TWITCH,
        ];
    }

    /**
     *
     */
    public function majTypeAndVideoId()
    {
        if (strpos($this->getUrl(), 'youtube')) {
            $this->setType(self::TYPE_YOUTUBE);
            $explode = explode('=', $this->getUrl());
            $this->setVideoId($explode[1]);
          } elseif (strpos($this->getUrl(), 'youtu.be')) {
            $this->setType(self::TYPE_YOUTUBE);
            $this->setVideoId(substr($this->getUrl(), strripos($this->getUrl(), '/') + 1, strlen($this->getUrl()) - 1));
        } elseif (strpos($this->getUrl(), 'twitch')) {
            $this->setType(self::TYPE_TWITCH);
            $explode = explode('/', $this->getUrl());
            $this->setVideoId($explode[count($explode) - 1]);
        } else {
            $this->setType(self::TYPE_UNKNOWN);
        }
    }


    /**
     * @return string
     */
    public function getEmbeddedUrl(): string
    {
        if ($this->getType() == self::TYPE_YOUTUBE) {
            return 'https://www.youtube.com/embed/' . $this->getVideoId();
        } elseif ($this->getType() == self::TYPE_TWITCH) {
            return 'https://player.twitch.tv/?autoplay=false&video=v' . $this->getVideoId() . '&parent=' .$_SERVER['SERVER_NAME'];
        } else {
            return $this->getUrl();
        }
    }

    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['libVideo'];
    }
}
