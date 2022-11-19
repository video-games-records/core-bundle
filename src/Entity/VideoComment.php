<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Comment
 * @ORM\Table(name="vgr_video_comment")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\VideoCommentRepository")
 * @ApiResource(attributes={"order"={"id"}})
 */
class VideoComment implements TimestampableInterface
{
    use TimestampableTrait;

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
     *   @ORM\JoinColumn(name="idVideo", referencedColumnName="id", nullable=false)
     * })
     */
    private Video $video;

    /**
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=false)
     * })
     */
    private Player $player;

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
    public function setId(int $id): Self
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
    public function setVideo(Video $video): Self
    {
        $this->video = $video;
        return $this;
    }

    /**
     * Get player
     * @return Player
     */
    public function getPlayer(): Player
    {
        return $this->player;
    }

    /**
     * Set player
     * @param Player $player
     * @return $this
     */
    public function setPlayer(Player $player): Self
    {
        $this->player = $player;
        return $this;
    }

    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text): Self
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
