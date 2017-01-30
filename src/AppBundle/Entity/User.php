<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @UniqueEntity("email")
 * @ORM\Table(name="users", uniqueConstraints={@ORM\UniqueConstraint(name="users_email_unique",columns={"email"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 */
class User implements UserInterface
{

    const MATCH_VALUE = 25;
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"user", "preference", "auth-token"})
     */
    private $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     * @Groups({"user", "preference", "auth-token"})
     */
    private $firstname;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     * @Groups({"user", "preference", "auth-token"})
     */
    private $lastname;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255)
     * @Groups({"user", "preference", "auth-token"})
     */
    private $email;


    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Preference", mappedBy="user")
     * @var
     * @Groups({"user"})
     */
    protected $preferences;

    /**
     * @ORM\Column(type="string")
     * @var
     */
    protected $password;


    /**
     * @Assert\NotBlank(groups={"New", "FullUpdate"})
     * @Assert\Type("string")
     * @Assert\Length(min="4", max="50")
     * @var
     */
    protected $plainPassword;

    public function __construct()
    {
        $this->preferences = new ArrayCollection();
    }

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
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    public function getPreferences()
    {
        return $this->preferences;
    }

    public function setPreferences($preferences)
    {
        $this->preferences = $preferences;
    }


    public function preferencesMAtch($themes)
    {
        $matchValue = 0;
        foreach ($this->getPreferences() as $pref){
            foreach ($themes as $theme){
                if ($pref->match($theme)){
                    $matchValue += $pref->getValue() * $theme->getValue();
                }
            }
        }
        return $matchValue > self::MATCH_VALUE;
    }

    public function getPassword()
    {
        return $this->password;
    }


    public function setPassword($password)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * @param mixed $plainPassword
     */
    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;
        return $this;

    }


    public function getRoles()
    {
        return [];
    }


    public function getSalt()
    {
        return null;
    }


    public function getUsername()
    {
        return $this->email;
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        $this->plainPassword = null;
    }
}

