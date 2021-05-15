<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;
use Knp\DoctrineBehaviors\Contract\Entity\TimestampableInterface;
use Knp\DoctrineBehaviors\Model\Timestampable\TimestampableTrait;

/**
 * PlayerChartStatus
 *
 * @ORM\Table(name="vgr_rule")
 * @ORM\Entity(repositoryClass="VideoGamesRecords\CoreBundle\Repository\RuleRepository")
 */
class Rule implements TranslatableInterface, TimestampableInterface
{
    use TranslatableTrait;
    use TimestampableTrait;

    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

      /**
     * @var string
     *
     * @Assert\Length(min="3",max="100")
     * @ORM\Column(name="name", type="string", length=100, nullable=false, unique=true)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Game", mappedBy="rules")
     * @ORM\JoinTable(name="vgr_rule_game",
     *      joinColumns={@ORM\JoinColumn(name="idRule", referencedColumnName="id")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="idGame", referencedColumnName="id")}
     *      )
     */
    private $games;

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
     * @return $this
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
     * Set namepseudo
     *
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }


    /**
     * @param string $text
     * @return $this
     */
    public function setText(string $text)
    {
        $this->translate(null, false)->setName($text);

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->translate(null, false)->getText();
    }

    /**
     * @return string
     */
    public function getDefaultText()
    {
        return $this->translate('en', false)->getText();
    }

    /**
     * @return mixed
     */
    public function getGames()
    {
        return $this->games;
    }
}
