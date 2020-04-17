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

/**
 * Video
 *
 * @ORM\Table(name="vgr_video", indexes={@ORM\Index(name="idxIdVideo", columns={"idVideo"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\VideoRepository")
 * @ORM\HasLifecycleCallbacks()
 * @ApiResource(attributes={"order"={"id": "ASC"}})
 * @ApiFilter(OrderFilter::class, properties={"id": "ASC"}, arguments={"orderParameterName"="order"})
 * @ApiFilter(BooleanFilter::class, properties={"boolActive"})
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
     * @ORM\Column(name="boolActive", type="boolean", nullable=false, options={"default":1})
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
     * @return string
     */
    public function __toString()
    {
        return sprintf('Video [%s]', $this->id);
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return Video
     */
    public function setId($id)
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
     *
     * @param boolean $boolActive
     * @return Video
     */
    public function setBoolActive($boolActive)
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
     *
     * @param string $type
     * @return Video
     */
    public function setType($type)
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
     *
     * @param string $libVideo
     * @return Video
     */
    public function setLibVideo($libVideo)
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
     *
     * @param string $url
     * @return Video
     */
    public function setUrl($url)
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
     * @param Game $game
     * @return Video
     */
    public function setGame(Game $game = null)
    {
        $this->game = $game;

        return $this;
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
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['libVideo'];
    }
}
