<?php

namespace AppBundle\Controller\Place;

use AppBundle\Entity\Place;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest; // alias pour toutes les annotations
use AppBundle\Form\Type\ThemeType;
use AppBundle\Entity\Theme;

class ThemeController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"theme"})
     * @Rest\Get("/places/{id}/themes")
     * @param Request $request
     */
    public function getThemesAction(Request $request)
    {
        $place = $this->getDoctrine()->getRepository(Place::class)->find($request->get('id'));
        /**
         * @var $place Place
         */
        if(empty($place)){
            return $this->placeNotFound();
        }
        return $place->getThemes();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"theme"})
     * @Rest\Post("/places/{id}/themes")
     * @param Request $request
     * @return static
     */
    public function postThemesAction(Request $request)
    {
        $place = $this->getDoctrine()->getRepository(Place::class)->find($request->get('id'));
        /**
         * @var $place Place
         */
        if(empty($place)){
            return $this->placeNotFound();
        }

        $theme = new Theme();
        $theme->setPlace($place);
        $form = $this->createForm(ThemeType::class, $theme);
        $form->submit($request->request->all());

        if($form->isValid()){
            $em = $this->getDoctrine()->getEntityManager();
            $em->persist($theme);
            $em->flush();
            return $theme;
        }else{
            return $form;
        }
    }

    private function placeNotFound()
    {
        return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
    }

}