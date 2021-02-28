<?php

namespace VideoGamesRecords\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use ApiPlatform\Core\Annotation\ApiResource;
use Knp\DoctrineBehaviors\Contract\Entity\TranslatableInterface;
use Knp\DoctrineBehaviors\Model\Translatable\TranslatableTrait;

/**
 * Country
 *
 * @ORM\Table(name="vgr_country")
 * @ORM\Entity
 * @ApiResource(attributes={"order"={"translations.name"}})
 */
class Country implements TranslatableInterface
{
    use TranslatableTrait;

    /**
     * @var string
     *
     * @Assert\Length(min="2", max="2")
     * @ORM\Column(name="code_iso2", type="string", length=2, nullable=false)
     */
    private $codeIso2;

    /**
     * @var string
     *
     * @Assert\Length(min="3", max="3")
     * @ORM\Column(name="code_iso3", type="string", length=3, nullable=false)
     */
    private $codeIso3;

    /**
     * @var string
     *
     * @ORM\Column(name="code_iso_numeric", type="integer", nullable=false)
     */
    private $codeIsoNumeric;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var Badge
     *
     * @ORM\OneToOne(targetEntity="VideoGamesRecords\CoreBundle\Entity\Badge")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="idBadge", referencedColumnName="id")
     * })
     */
    private $badge;

    /**
     * Set codeIso
     *
     * @param string $codeIso2
     * @return Country
     */
    public function setCodeIso2(string $codeIso2)
    {
        $this->codeIso2 = $codeIso2;

        return $this;
    }

    /**
     * Get codeIso
     *
     * @return string
     */
    public function getCodeIso2()
    {
        return $this->codeIso2;
    }

    /**
     * @return string
     */
    public function getCodeIso3()
    {
        return $this->codeIso3;
    }

    /**
     * @param string $codeIso3
     * @return Country
     */
    public function setCodeIso3(string $codeIso3)
    {
        $this->codeIso3 = $codeIso3;
        return $this;
    }

    /**
     * @return string
     */
    public function getCodeIsoNumeric()
    {
        return $this->codeIsoNumeric;
    }

    /**
     * @param string $codeIsoNumeric
     * @return Country
     */
    public function setCodeIsoNumeric(string $codeIsoNumeric)
    {
        $this->codeIsoNumeric = $codeIsoNumeric;
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setName(string $name)
    {
        $this->translate(null, false)->setName($name);

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->translate(null, false)->getName();
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
     * Set id
     *
     * @param int $id
     * @return $this
     */
    public function setId(int $id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * set badge
     * @param Badge|null $badge
     * @return $this
     */
    public function setBadge(Badge $badge = null)
    {
        $this->badge = $badge;

        return $this;
    }

    /**
     * Get idBadge
     *
     * @return Badge
     */
    public function getBadge()
    {
        return $this->badge;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s [%d]', $this->getDefaultName(), $this->getId());
    }

    /**
     * @return string
     */
    public function getDefaultName()
    {
        return $this->translate('en', false)->getName();
    }
}
