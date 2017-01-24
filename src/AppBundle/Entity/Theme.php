<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * Theme
 *
 * @ORM\Table(name="theme", uniqueConstraints={@ORM\UniqueConstraint(name="themes_name_place_unique", columns={"name", "place_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ThemeRepository")
 */
class Theme
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"theme", "place"})
     */
    private $id;

    /**
     * @Assert\NotNull()
     * @Assert\Choice({"art", "architecture", "history", "sf", "sport"})
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"theme", "place"})
     */
    private $name;

    /**
     * @Assert\NotNull()
     * @Assert\Type("numeric")
     * @Assert\GreaterThan(value="0")
     * @Assert\LessThanOrEqual(value="10")
     * @var int
     *
     * @ORM\Column(name="value", type="integer")
     * @Groups({"theme", "place"})
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="themes")
     * @var
     * @Groups({"theme"})
     */
    protected $place;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Theme
     */
    public function setName($name)
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
     * Set value
     *
     * @param integer $value
     *
     * @return Theme
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return int
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getPlace()
    {
        return $this->place;
    }

    public function setPlace($place)
    {
        $this->place = $place;
        return $this;
    }
}

