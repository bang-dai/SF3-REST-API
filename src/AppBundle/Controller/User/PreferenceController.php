<?php

namespace AppBundle\Controller\User;

use AppBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use AppBundle\Form\Type\PreferenceType;
use AppBundle\Entity\Preference;

class PreferenceController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"preference"})
     * @Rest\Get("/users/{id}/preferences")
     * @param Request $request
     */
    public function getPreferencesAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
        /**
         * @var $user User
         */
        if(empty($user)){
            return $this->userNotFound();
        }
        return $user->getPreferences();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"preference"})
     * @Rest\Post("/users/{id}/preferences")
     * @param Request $request
     * @return static
     */
    public function postPreferencesAction(Request $request)
    {
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->get('id'));
        /**
         * @var $user User
         */
        if(empty($user)){
            return $this->userNotFound();
        }

        $pref = new Preference();
        $pref->setUser($user);
        $form = $this->createForm(PreferenceType::class, $pref);
        $form->submit($request->request->all());

        if($form->isValid()){
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($pref);
            $em->flush();
            return $pref;
        }else{
            return $form;
        }
    }

    private function userNotFound()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}