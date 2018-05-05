<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Player as PlayerModel;
use VideoGamesRecords\CoreBundle\Model\Game as GameModel;

/**
 * GameTopic
 *
 * @ORM\Table(name="vgr_game_topic", indexes={@ORM\Index(name="idxTopic", columns={"idTopic"}), @ORM\Index(name="idxPlayer", columns={"idPlayer"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameTopicRepository")
 */
class GameTopic
{
    use Timestampable;
    use PlayerModel;
    use GameModel;

    /**
     * @var integer
     *
     * @ORM\Column(name="idTopic", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idTopic;

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
        return sprintf('%s [%s]', $this->getLibTopic(), $this->idTopic);
    }

    /**
     * Set idTopic
     *
     * @param integer $idTopic
     * @return GameTopic
     */
    public function setIdTopic($idTopic)
    {
        $this->idTopic = $idTopic;
        return $this;
    }

    /**
     * Get idTopic
     *
     * @return integer
     */
    public function getIdTopic()
    {
        return $this->idTopic;
    }

    /**
     * Set libTopic
     *
     * @param string $libTopic
     * @return GameTopic
     */
    public function setLibTopic($libTopic)
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
     * @return $this
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
}
