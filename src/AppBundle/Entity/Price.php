<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use JMS\Serializer\Annotation\Groups;

/**
 * Price
 *
 * @ORM\Table(name="price", uniqueConstraints={@ORM\UniqueConstraint(name="prices_type_unique", columns={"type", "place_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\PriceRepository")
 */
class Price
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Groups({"price", "place"})
     */
    private $id;

    /**
     * @var string
     * @Assert\NotNull()
     * @Assert\Choice({"less_than_12", "for_all"})
     * @ORM\Column(name="type", type="string", length=255)
     * @Groups({"price", "place"})
     */
    private $type;

    /**
     * @var float
     * @Assert\NotNull()
     * @Assert\Type("numeric")
     * @Assert\GreaterThanOrEqual(value = 0)
     * @ORM\Column(name="value", type="float")
     * @Groups({"price", "place"})
     */
    private $value;


    /**
     * @ORM\ManyToOne(targetEntity="AppBundle\Entity\Place", inversedBy="prices")
     * @Groups({"price"})
     * @var
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
     * Set type
     *
     * @param string $type
     *
     * @return Price
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set value
     *
     * @param float $value
     *
     * @return Price
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return float
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

