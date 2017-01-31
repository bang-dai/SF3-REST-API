<?php
/**
 * Created by PhpStorm.
 * User: bdai
 * Date: 31/01/2017
 * Time: 15:42
 */

namespace AppBundle\Security;


use AppBundle\Entity\AuthToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Http\HttpUtils;

class AuthTokenAuthenticator implements SimplePreAuthenticatorInterface, AuthenticationFailureHandlerInterface
{
    /**
     * Token validity duration in seconde : 12 hours
     */
    const TOKEN_VALIDITY = 12 * 3600;

    private $httpUtils;

    public function __construct(HttpUtils $httpUtils)
    {
        $this->httpUtils = $httpUtils;
    }

    public function createToken(Request $request, $providerKey)
    {
        
        if($request->getMethod() === 'POST' && $this->httpUtils->checkRequestPath($request, '/auth-tokens')){
            return; //we don't need to check if the request call is for creatinf the token
        }

        $authTokenValue = $request->headers->get('X-Auth-Token');
        if(!$authTokenValue){
            throw new BadCredentialsException('X-Auth-Token header is required');
        }

        return new PreAuthenticatedToken(
            'anon.',
            $authTokenValue,
            $providerKey
        );

    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if(!$userProvider instanceof  AuthTokenUserProvider){
            throw new \InvalidArgumentException(
                sprintf(
                    'The user provider must be an instance of AuthTokenUserProvider (%s was given).',
                    get_class($userProvider)
                )
            );
        }
        
        $authTokenValue = $token->getCredentials();
        /** @var AuthToken $authToken */
        $authToken = $userProvider->getAuthToken($authTokenValue);

        if(!$authToken || !$this->isTokenValid($authToken)){
            throw new BadCredentialsException('Invalid authentication token');
        }
        
        $user = $authToken->getUser();

        $pre = new PreAuthenticatedToken(
            $user,
            $authTokenValue,
            $providerKey,
            $user->getRoles()
        );

        $pre->setAuthenticated(true); //because our users don't have particular roles. We have to force the authentification

        return $pre;
        
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof PreAuthenticatedToken && $token->getProviderKey() === $providerKey;
    }

    /**
     * This is called when an interactive authentication attempt fails. This is
     * called by authentication listeners inheriting from
     * AbstractAuthenticationListener.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     *
     * @return Response The response to return, never null
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        // Si les données d'identification ne sont pas correctes, une exception est levée
        throw $exception;
    }

    private function isTokenValid(AuthToken $authToken)
    {
        return (time() - $authToken->getCreatedAt()->getTimestamp() < self::TOKEN_VALIDITY);
    }
}