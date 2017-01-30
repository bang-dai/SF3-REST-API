<?php


namespace AppBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

class Credentials
{

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @var
     */
    private $login;

    /**
     * @Assert\NotBlank()
     * @Assert\Type("string")
     * @var
     */
    private $password;

    /**
     * @return mixed
     */
    public function getLogin()
    {
        return $this->login;
    }

    /**
     * @param mixed $login
     */
    public function setLogin($login)
    {
        $this->login = $login;
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

}