<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * PlayerChartStatus
 *
 * @ORM\Table(name="vgr_rule")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\RuleRepository")
 */
class Rule implements TranslatableInterface
{
    use TranslatableTrait;
    use TimestampableEntity;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private ?int $id = null;

    /**
     * @Assert\Length(min="3",max="100")
     * @ORM\Column(name="name", type="string", length=100, nullable=false, unique=true)
     */
    private string $name;

    /**
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Player")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idPlayer", referencedColumnName="id", nullable=true)
     * })
     */
    private ?Player $player;

    /**
     * @ORM\ManyToMany(targetEntity="Game", mappedBy="rules")
     * @ORM\JoinTable(name="vgr_rule_game",
     *      joinColumns={@ORM\JoinColumn(name="idRule", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idGame", referencedColumnName="id")}
     *      )
     */
    private Collection $games;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->games = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getName(), $this->id);
    }

    /**
     * Set id
     * @param integer $id
     * @return Rule
     */
    public function setId(int $id): Rule
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Set namepseudo
     *
     * @param string $name
     * @return Rule
     */
    public function setName(string $name): Rule
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * Set player
     *
     * @param Player $player
     * @return Rule
     */
    public function setPlayer(Player $player): Rule
    {
        $this->player = $player;

        return $this;
    }

    /**
     * Get player
     * @return Player|null
     */
    public function getPlayer(): ?Player
    {
        return $this->player;
    }


    /**
     * @param string $text
     * @return Rule
     */
    public function setText(string $text): Rule
    {
        $this->translate(null, false)->setName($text);

        return $this;
    }

    /**
     * @return string|null
     */
    public function getText(): ?string
    {
        return $this->translate(null, false)->getText();
    }

    /**
     * @return string
     */
    public function getDefaultText(): string
    {
        return $this->translate('en', false)->getText();
    }

    /**
     * @return Collection
     */
    public function getGames(): Collection
    {
        return $this->games;
    }
}
