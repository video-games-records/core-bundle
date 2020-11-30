<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Player;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\BooleanFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Video
 *
 * @ORM\Table(name="vgr_video")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\VideoRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(attributes={"order"={"id": "ASC"}})
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(BooleanFilter::class, properties={"boolActive"})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "libVideo": "partial"
 *      }
 * )
 * @DoctrineAssert\UniqueEntity(fields={"url"})
 */
class Video implements TimestampableInterface, SluggableInterface
{
    use TimestampableTrait;
    use SluggableTrait;
    use Player;

    const TYPE_YOUTUBE = 'Youtube';
    const TYPE_TWITCH = 'Twitch';
    const TYPE_UNKNOWN = 'Unknown';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="boolActive", type="boolean", nullable=false, options={"default":true})
     */
    private $boolActive = true;


    /**
     * @var string
     *
     * @ORM\Column(name="type", type="string", nullable=false)
     */
    private $type = self::TYPE_YOUTUBE;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     * @ORM\Column(name="url", type="string", length=255, nullable=false)
     */
    private $url;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="200")
     * @ORM\Column(name="libVideo", type="string", length=200, nullable=false)
     */
    private $libVideo;

    /**
     * @var integer
     *
     * @ORM\Column(name="nbComment", type="integer", nullable=false, options={"default":0})
     */
    private $nbComment = 0;

    /**
     * @var Game
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="videos")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\VideoComment", mappedBy="video")
     */
    private $comments;



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
     * Set boolActive
     * @param boolean $boolActive
     * @return Video
     */
    public function setBoolActive(bool $boolActive)
    {
        $this->boolActive = $boolActive;

        return $this;
    }

    /**
     * Get boolActive
     *
     * @return boolean
     */
    public function getBoolActive()
    {
        return $this->boolActive;
    }

    /**
     * Set type
     * @param string $type
     * @return Video
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
     * Set libVideo
     * @param string $libVideo
     * @return Video
     */
    public function setLibVideo(string $libVideo)
    {
        $this->libVideo = $libVideo;

        return $this;
    }

    /**
     * Get libVideo
     *
     * @return string
     */
    public function getLibVideo()
    {
        return $this->libVideo;
    }

    /**
     * Set url
     * @param string $url
     * @return Video
     */
    public function setUrl(string $url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set game
     * @param Game|null $game
     * @return Video
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;

        return $this;
    }

    /**
     * Set nbComment
     * @param integer $nbComment
     * @return $this
     */
    public function setNbComment(int $nbComment)
    {
        $this->nbComment = $nbComment;

        return $this;
    }

    /**
     * Get nbComment
     *
     * @return integer
     */
    public function getNbComment()
    {
        return $this->nbComment;
    }

    /**
     * Get game
     * @return Game
     */
    public function getGame()
    {
        return $this->game;
    }

    /**
     * @return mixed
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * @return array
     */
    public static function getTypeChoices()
    {
        return [
            self::TYPE_YOUTUBE => self::TYPE_YOUTUBE,
            self::TYPE_TWITCH => self::TYPE_TWITCH,
        ];
    }

    /**
     * @ORM\PrePersist
     */
    public function preInsert()
    {
        if (strpos($this->getUrl(), 'youtube')) {
            $this->setType(self::TYPE_YOUTUBE);
        } elseif (strpos($this->getUrl(), 'twitch')) {
            $this->setType(self::TYPE_TWITCH);
        } elseif (strpos($this->getUrl(), 'twitch')) {
            $this->setType(self::TYPE_UNKNOWN);
        }
    }

    /**
     * @return string
     */
    public function getEmbeddedUrl()
    {
        if ($this->getType() == self::TYPE_YOUTUBE) {
            return 'https://www.youtube.com/embed/' . $this->getYoutubeId();
        } elseif ($this->getType() == self::TYPE_TWITCH) {
            return 'https://player.twitch.tv/?autoplay=false&video=v' . $this->getTwitchId();
        } else {
            return $this->getUrl();
        }
    }

    /**
     * @return mixed|string|null
     */
    public function getVideoId()
    {
        if ($this->getType() == self::TYPE_YOUTUBE) {
            return $this->getYoutubeId();
        } elseif ($this->getType() == self::TYPE_TWITCH) {
            return $this->getTwitchId();
        }
        return null;
    }

    /**
     * @return mixed|string
     */
    public function getYoutubeId()
    {
        $explode = explode('=', $this->getUrl());
        return isset($explode[1]) ? $explode[1] : null;
    }

    /**
     * @return mixed|string
     */
    public function getTwitchId()
    {
        $explode = explode('/', $this->getUrl());
        return $explode[count($explode) - 1];
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
