<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Player as PlayerModel;
use VideoGamesRecords\CoreBundle\Model\Game as GameModel;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * GameTopic
 *
 * @ORM\Table(name="vgr_game_topic")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameTopicRepository")
 */
class GameTopic implements TimestampableInterface
{
    use TimestampableTrait;
    use PlayerModel;
    use GameModel;

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
     * @Assert\NotBlank()
     * @Assert\Length(min="5", max="255")
     * @ORM\Column(name="libTopic", type="string", length=255, nullable=false)
     */
    private $libTopic;

    /**
     * @ORM\OneToMany(targetEntity="VideoGamesRecords\CoreBundle\Entity\GameMessage", mappedBy="topic", cascade={"persist", "remove"}, orphanRemoval=true)
     */
    private $messages;

    /**
     * @var Game
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Game", inversedBy="topics")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idGame", referencedColumnName="id", nullable=false)
     * })
     */
    private $game;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->messages = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%s]', $this->getLibTopic(), $this->id);
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return GameTopic
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
     * Set libTopic
     *
     * @param string $libTopic
     * @return GameTopic
     */
    public function setLibTopic(string $libTopic)
    {
        $this->libTopic = $libTopic;

        return $this;
    }

    /**
     * Get libTopic
     *
     * @return string
     */
    public function getLibTopic()
    {
        return $this->libTopic;
    }

    /**
     * @param GameMessage $message
     * @return GameTopic
     */
    public function addMessage(GameMessage $message)
    {
        $message->setTopic($this);
        $this->messages[] = $message;
        return $this;
    }

    /**
     * @param GameMessage $message
     */
    public function removeMessage(GameMessage $message)
    {
        $this->messages->removeElement($message);
    }

    /**
     * @return mixed
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * Set game
     * @param Game|null $game
     * @return GameTopic
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
}
