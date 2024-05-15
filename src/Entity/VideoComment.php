<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Link;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\VideoCommentRepository;
use VideoGamesRecords\CoreBundle\Traits\Entity\Player\PlayerTrait;

#[ORM\Table(name:'vgr_video_comment')]
#[ORM\Entity(repositoryClass: VideoCommentRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\VideoCommentListener"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['video-comment:insert']],
            security: 'is_granted("ROLE_PLAYER")'
        ),
    ],
    normalizationContext: ['groups' => ['video-comment:read', 'video-comment:player', 'player:read']]
)]
#[ApiResource(
    uriTemplate: '/videos/{id}/comments',
    uriVariables: [
        'id' => new Link(fromClass: Video::class, toProperty: 'video'),
    ],
    operations: [ new GetCollection() ],
    normalizationContext: ['groups' => ['video-comment:read', 'video-comment:player', 'player:read']],
)]
class VideoComment
{
    use TimestampableEntity;
    use PlayerTrait;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Video::class, inversedBy: 'comments')]
    #[ORM\JoinColumn(name:'video_id', referencedColumnName:'id', nullable:false, onDelete: 'CASCADE')]
    private Video $video;

    #[Assert\NotBlank]
    #[ORM\Column(type: 'text', nullable: false)]
    private string $text;

    public function __toString()
    {
        return sprintf('comment [%s]', $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVideo(): Video
    {
        return $this->video;
    }

    public function setVideo(Video $video): void
    {
        $this->video = $video;
    }


    public function setText(string $text): void
    {
        $this->text = $text;
    }

    public function getText(): string
    {
        return $this->text;
    }
}
