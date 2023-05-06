<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\SluggableInterface;
use Knp\DoctrineBehaviors\Model\Sluggable\SluggableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Game
 *
 * @ORM\Table(name="vgr_platform")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\PlatformRepository")
 * @ApiResource(attributes={"order"={"libPlatform"}})
 * @ApiFilter(
 *     SearchFilter::class,
 *     properties={
 *          "status": "exact",
 *          "playerPlatform.player": "exact",
 *          "games.playerGame.player": "exact",
 *      }
 * )
 *
 */
class Platform implements SluggableInterface
{
    use SluggableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\Length(max="100")
     * @ORM\Column(name="libPlatform", type="string", length=100, nullable=false)
     */
    private string $libPlatform = '';

    /**
     * @Assert\Length(max="30")
     * @ORM\Column(name="picture", type="string", length=30, nullable=false)
     */
    private string $picture = 'bt_default.png';

    /**
     * @ORM\Column(name="status", type="string", length=30, nullable=false)
     */
    private string $status = 'INACTIF';


    /**
     * @ORM\ManyToMany(targetEntity="Game", mappedBy="platforms")
     * @ORM\JoinTable(name="vgr_game_platform",
     *      joinColumns={@ORM\JoinColumn(name="idPlatform", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idGame", referencedColumnName="id")}
     *      )
     */
    private $games;


    /**
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Badge", inversedBy="platform",cascade={"persist"}))
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id")
     * })
     */
    private ?Badge $badge;


    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->libPlatform, $this->id);
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return $this
     */
    public function setId(int $id): static
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
     * Get libPlatform
     *
     * @return string
     */
    public function getLibPlatform(): string
    {
        return $this->libPlatform;
    }

    /**
     * Set libPlaform
     *
     * @param string $libPlatform
     * @return $this
     */
    public function setLibPlatform(string $libPlatform): static
    {
        $this->libPlatform = $libPlatform;

        return $this;
    }

    /**
     * Set picture
     *
     * @param string $picture
     * @return $this
     */
    public function setPicture(string $picture): static
    {
        $this->picture = $picture;

        return $this;
    }

    /**
     * Get picture
     *
     * @return string
     */
    public function getPicture(): ?string
    {
        return $this->picture;
    }

    /**
     * Set status
     *
     * @param string $status
     * @return $this
     */
    public function setStatus(string $status): static
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getGames()
    {
        return $this->games;
    }

    /**
     * Set badge
     *
     * @param $badge
     * @return $this
     */
    public function setBadge($badge = null): static
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get idBadge
     *
     * @return Badge
     */
    public function getBadge(): ?Badge
    {
        return $this->badge;
    }

    /**
     * Returns an array of the fields used to generate the slug.
     *
     * @return string[]
     */
    public function getSluggableFields(): array
    {
        return ['libPlatform'];
    }
}
