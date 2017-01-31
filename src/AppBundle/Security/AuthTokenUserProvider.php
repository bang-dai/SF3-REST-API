<?php

namespace AppBundle\Security;


use AppBundle\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class AuthTokenUserProvider implements UserProviderInterface
{

    private $authTokenRepository;
    private $userRepository;

    public function __construct(EntityRepository $authTokenRepository, EntityRepository $userRepository)
    {
        $this->authTokenRepository = $authTokenRepository;
        $this->userRepository = $userRepository;
    }
    
    
    public function getAuthToken($value)
    {
        return $this->authTokenRepository->findOneByValue($value);
    }

    public function loadUserByUsername($email)
    {
        return $this->userRepository->findByEmail($email);
    }

    public function refreshUser(UserInterface $user)
    {
        // system stateless, donc cette methode ne doit jamais être appelé
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return 'AppBundle\Entiry\User' === $class;
    }
}