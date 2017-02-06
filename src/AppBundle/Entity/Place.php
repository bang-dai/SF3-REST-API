<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use AppBundle\Form\Validator\Constraint as MyAssert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use JMS\Serializer\Annotation\Groups;

/**
 * @UniqueEntity("name")
 * @ORM\Entity()
 * @ORM\Table(name="places", uniqueConstraints={@ORM\UniqueConstraint(name="places_name_unique", columns={"name"})})
 * Class Place
 * @package AppBundle\Entity
 */
class Place
{

    /**
     * Place id
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     * @var
     * @Groups({"price", "place", "theme"})
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     * @var
     * @Groups({"price", "place", "theme"})
     */
    protected $name;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @ORM\Column(type="string")
     * @var
     * @Groups({"price", "place", "theme"})
     */
    protected $address;

    /**
     * @Assert\Valid()
     * @MyAssert\PriceTypeUnique()
     * @ORM\OneToMany(targetEntity="Price", mappedBy="place")
     * @var Price[]
     * @Groups({"place"})
     */
    protected $prices;

    /**
     * @ORM\OneToMany(targetEntity="AppBundle\Entity\Theme", mappedBy="place")
     * @var
     * @Groups({"place"})
     */
    protected $themes;


    public function __construct()
    {
        $this->prices = new ArrayCollection();
        $this->themes = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getAddress()
    {
        return $this->address;
    }

    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function setAddress($address)
    {
        $this->address = $address;
        return $this;
    }

    public function getPrices()
    {
        return $this->prices;
    }

    public function setPrices($prices)
    {
        $this->prices = $prices;
        return $this;
    }

    public function getThemes()
    {
        return $this->themes;
    }

    public function setThemes($themes)
    {
        $this->themes = $themes;
    }

}