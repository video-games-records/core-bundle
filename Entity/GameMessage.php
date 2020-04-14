<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use VideoGamesRecords\CoreBundle\Model\Player as PlayerModel;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * GameMessage
 *
 * @ORM\Table(name="vgr_game_message", indexes={@ORM\Index(name="idxMessage", columns={"id"})})
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\GameMessageRepository")
 */
class GameMessage implements TimestampableInterface
{
    use TimestampableTrait;
    use PlayerModel;

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
     * @ORM\Column(name="text", type="text", nullable=false)
     */
    private $text;

    /**
     * @var GameTopic
     *
     * @Assert\NotNull
     * @ORM\ManyToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\GameTopic", inversedBy="messages")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idTopic", referencedColumnName="id", nullable=false)
     * })
     */
    private $topic;

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('Message [%d]', $this->id);
    }

    /**
     * Set id
     *
     * @param integer $id
     * @return GameMessage
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
