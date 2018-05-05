<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Knp\DoctrineBehaviors\Model\Timestampable\Timestampable;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Player as PlayerModel;

/**
 * GameMessage
 *
 * @ORM\Table(name="vgr_game_message", indexes={@ORM\Index(name="idxMessage", columns={"idMessage"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameMessageRepository")
 */
class GameMessage
{
    use Timestampable;
    use PlayerModel;

    /**
     * @var integer
     *
     * @ORM\Column(name="idMessage", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $idMessage;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var GameTopic
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\GameTopic", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTopic", referencedColumnName="idTopic", nullable=false)
     * })
     */
    private $topic;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Message [%d]', $this->idMessage);
    }

    /**
     * Set idMessage
     *
     * @param integer $idMessage
     * @return GameMessage
     */
    public function setIdMessage($idMessage)
    {
        $this->idMessage = $idMessage;
        return $this;
    }

    /**
     * Get idMessage
     *
     * @return integer
     */
    public function getIdMessage()
    {
        return $this->idMessage;
    }

    /**
     * Set text
     *
     * @param string $text
     * @return GameMessage
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * Set topic
     * @param GameTopic $topic
     * @return GameMessage
     */
    public function setTopic(GameTopic $topic = null)
    {
        $this->topic = $topic;

        return $this;
    }

    /**
     * Get topic
     * @return GameTopic
     */
    public function getTopic()
    {
        return $this->topic;
    }
}
