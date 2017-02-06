<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * Preference
 *
 * @ORM\Table(name="preference", uniqueConstraints={@ORM\UniqueConstraint(name="preferences_name_user_unique", columns={"name", "user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PreferenceRepository")
 */
class Preference
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"user", "preference"})
     */
    private $id;

    /**
     * @Assert\NotNull()
     * @Assert\Choice({"art", "architecture", "history", "sf", "sport"})
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     * @Groups({"user", "preference"})
     */
    private $name;

    /**
     * @Assert\NotNull()
     * @Assert\Type("numeric")
     * @Assert\GreaterThan(value="0")
     * @Assert\LessThanOrEqual(value="10")
     * @var string
     *
     * @ORM\Column(name="value", type="string", length=255)
     * @Groups({"user", "preference"})
     */
    private $value;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\User", inversedBy="preferences")
     * @var
     * @Groups({"preference"})
     */
    protected $user;

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
     * @return Preference
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
     * @param string $value
     *
     * @return Preference
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function match(Theme $theme)
    {
        return $this->name === $theme->getName();
    }
}

