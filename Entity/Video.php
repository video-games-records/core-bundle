<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use VideoGamesRecords\CoreBundle\Model\Player;
use VideoGamesRecords\CoreBundle\Model\Game;

/**
 * Video
 *
 * @ORM\Table(name="vgr_video", indexes={@ORM\Index(name="idxIdVideo", columns={"idVideo"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\VideoRepository")
 */
class Video
{
    use Timestampable;
    use Player;
    use Game;

    const STATUS_OK = 'OK';
    const STATUS_ERROR = 'ERROR';

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;


    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", nullable=false)
     */
    private $status = self::STATUS_OK;

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
     * @var \Symfony\Component\HttpFoundation\File\UploadedFile
     *
     * @Assert\File(
     *     maxSize = "500M",
     *     mimeTypes = {"video/mpeg", "video/mp4", "video/quicktime", "video/x-ms-wmv", "video/x-msvideo", "video/x-flv"},
     * )
     */
    public $file;

    /**
     * @var string
     *
     * @Assert\Length(min="5", max="100")
     * @ORM\Column(name="fileIn", type="string", length=100, nullable=false)
     */
    private $fileIn;

    /**
     * @var string
     *
     * @Assert\Length(min="5", max="100")
     * @ORM\Column(name="fileOut", type="string", length=100, nullable=false)
     */
    private $fileOut;

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
     * @return $this
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
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set libVideo
     *
     * @param string $libVideo
     * @return $this
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
     * @return $this
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
     * @return array
     */
    public static function getStatusChoices()
    {
        return [
            self::STATUS_OK => self::STATUS_OK,
            self::STATUS_ERROR => self::STATUS_ERROR,
        ];
    }
}
