<?php

declare(strict_types=1);

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Repository\ProofRequestRepository;
use VideoGamesRecords\CoreBundle\ValueObject\ProofRequestStatus;

#[ORM\Table(name:'vgr_proof_request')]
#[ORM\Entity(repositoryClass: ProofRequestRepository::class)]
#[ORM\EntityListeners(["VideoGamesRecords\CoreBundle\EventListener\Entity\ProofRequestListener"])]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['proof-request:insert']],
        ),
        new Put(
            denormalizationContext: ['groups' => ['proof-request:update']],
            security: 'is_granted("ROLE_PLAYER") and object.getPlayerChart().getStatus().getId() == 1'
        ),
    ],
    normalizationContext: ['groups' => ['proof-request:read']],
)]
class ProofRequest
{
    use TimestampableEntity;

    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    private ?int $id = null;

    #[Assert\Length(max: 50)]
    #[ORM\Column(length: 50, nullable: false)]
    private string $status = ProofRequestStatus::IN_PROGRESS;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $response = null;

    #[ORM\Column(type: 'text', nullable: true)]
    private ?string $message = null;

    #[ORM\Column(nullable: true)]
    private ?Datetime $dateAcceptance = null;

    #[ORM\ManyToOne(targetEntity: PlayerChart::class)]
    #[ORM\JoinColumn(name:'player_chart_id', referencedColumnName:'id', nullable:true, onDelete:'CASCADE')]
    private PlayerChart $playerChart;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'requesting_player_id', referencedColumnName:'id', nullable:false)]
    private Player $playerRequesting;

    #[ORM\ManyToOne(targetEntity: Player::class)]
    #[ORM\JoinColumn(name:'responding_player_id', referencedColumnName:'id', nullable:true)]
    private ?Player $playerResponding = null;

    public function __toString()
    {
        return sprintf('Request [%s]', $this->id);
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setResponse(string $response): void
    {
        $this->response = $response;
    }

    public function getResponse(): ?string
    {
        return $this->response;
    }

    public function setDateAcceptance(DateTime $dateAcceptance): void
    {
        $this->dateAcceptance = $dateAcceptance;
    }

    public function getDateAcceptance(): ?DateTime
    {
        return $this->dateAcceptance;
    }

    public function setMessage(string $message): void
    {
        $this->message = $message;
    }

    public function getMessage(): ?string
    {
        return $this->message;
    }

    public function setPlayerChart(PlayerChart $playerChart): void
    {
        $this->playerChart = $playerChart;
    }

    public function getPlayerChart(): PlayerChart
    {
        return $this->playerChart;
    }

    public function setPlayerRequesting(Player $playerRequesting): void
    {
        $this->playerRequesting = $playerRequesting;
    }

    public function getPlayerRequesting(): Player
    {
        return $this->playerRequesting;
    }

    public function setPlayerResponding(?Player $playerResponding = null): void
    {
        $this->playerResponding = $playerResponding;
    }

    public function getPlayerResponding(): ?Player
    {
        return $this->playerResponding;
    }
}
