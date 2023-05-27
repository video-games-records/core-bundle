<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Entity\Player\PlayerTrait;

/**
 * Comment
 * @ORM\Table(name="vgr_video_comment")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\VideoCommentRepository")
 * @ORM\EntityListeners({"VideoGamesRecords\CoreBundle\EventListener\Entity\VideoCommentListener"})
 * @ApiResource(attributes={"order"={"id"}})
 */
class VideoComment
{
    use TimestampableEntity;
    use PlayerTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Video", inversedBy="comments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idVideo", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     * })
     */
    private Video $video;

    /**
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private string $text;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('comment [%s]', $this->id);
    }

    /**
     * Set id
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): VideoComment
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
     * Get video
     * @return Video
     */
    public function getVideo(): Video
    {
        return $this->video;
    }

    /**
     * Set video
     * @param Video $video
     * @return $this
     */
    public function setVideo(Video $video): VideoComment
    {
        $this->video = $video;
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): VideoComment
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText(): string
    {
        return $this->text;
    }
}
