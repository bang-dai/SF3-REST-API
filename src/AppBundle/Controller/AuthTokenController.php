<?php
/**
 * Created by PhpStorm.
 * User: bdai
 * Date: 30/01/2017
 * Time: 20:08
 */

namespace AppBundle\Controller;


use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use AppBundle\Form\Type\CredentialsType;
use AppBundle\Entity\AuthToken;
use AppBundle\Entity\Credentials;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class AuthTokenController extends Controller
{

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"auth-token"})
     * @Rest\Post("/auth-tokens")
     * @param Request $request
     */
    public function postAuthTokensAction(Request $request)
    {
        $credentials = new Credentials();
        $form = $this->createForm(CredentialsType::class, $credentials);
        $form->submit($request->request->all());

        if(!$form->isValid())
            return $form;

        $em = $this->getDoctrine()->getManager();

        $user = $em->getRepository(User::class)->findOneByEmail($credentials->getLogin());
        if(!$user)
            return $this->invalidCredentials();

        $encoder = $this->get('security.password_encoder');
        $isPasswordValid =$encoder->isPasswordValid($user, $credentials->getPassword());

        if(!$isPasswordValid)
            return $this->invalidCredentials();

        $authToken = new AuthToken();
        $authToken->setValue(base64_encode(random_bytes(50)));
        $authToken->setCreatedAt(new \DateTime('now'));
        $authToken->setUser($user);

        $em->persist($authToken);
        $em->flush();

        return $authToken;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/auth-tokens/{id}")
     * @param Request $request
     */
    public function removeAuthTokenAction(Request $request)
    {
        $em = $this->getDoctrine()->getmanager();
        /** @var AuthToken $authToken */
        $authToken = $em->getRepository(AuthToken::class)->find($request->get('id'));

        /** @var User $connectedUser */
        $connectedUser = $this->get('security.token_storage')->getToken()->getUser();

        if($authToken && $authToken->getUser()->getId() === $connectedUser->getId()){
            $em->remove($authToken);
            $em->flush();
        }else{
            throw new \Symfony\Component\HttpKernel\Exception\BadRequestHttpException();
        }
    }

    private function invalidCredentials()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Invalid credentials'], Response::HTTP_BAD_REQUEST);
    }

}