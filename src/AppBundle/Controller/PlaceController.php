<?php
/**
 * Created by PhpStorm.
 * User: bdai
 * Date: 21/01/2017
 * Time: 19:54
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Place;
use AppBundle\Form\Type\PlaceType;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PlaceController extends Controller
{


    /**
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Get("/places")
     * @param Request $request
     */
    public function getPlacesAction(Request $request)
    {

        $places = $this->getDoctrine()->getRepository(Place::class)->findAll();

        return $places;
    }


    /**
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Get("/places/{id}")
     * @param $id
     * @param Request $request
     */
    public function getPlaceAction(Request $request)
    {
        $place = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:Place')
            ->find($request->get('id'));
        /* @var $place Place */

        if(empty($place)){
            return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

       return $place;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"place"})
     * @Rest\Post("/places")
     * @param Request $request
     */
    public function postPlacesAction(Request $request)
    {
        $place = new Place();
        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all());

        if($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            foreach($place->getPrices() as $price){
                $price->setPlace($place);
                $em->persist($price);
            }
            $em->persist($place);
            $em->flush();
            return $place;
        }else{
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"place"})
     * @Rest\Delete("/places/{id}")
     * @param Request $request
     */
    public function removePlaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $place = $em->getRepository(Place::class)->find($request->get('id'));

        if($place) {
            foreach ($place->getPrices() as $price){
                $em->remove($price);
            }

            $em->remove($place);
            $em->flush();
        }
    }

    /**
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Put("/places/{id}")
     * @param Request $request
     */
    public function putPlaceAction(Request $request)
    {
        return $this->updatePlace($request, true);
    }


    /**
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Patch("/places/{id}")
     * @param Request $request
     * @return Place|\Symfony\Component\Form\Form|JsonResponse
     */
    public function patchPlaceAction(Request $request)
    {
       return $this->updatePlace($request, false);
    }

    private function updatePlace(Request $request, $clearMessing)
    {
        $place = $this->getDoctrine()->getEntityManager()->getRepository(Place::class)->find($request->get('id'));
        if(empty($place)){
            return \FOS\RestBundle\View\View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all(), $clearMessing);

        if($form->isValid()){
            $em = $this->getDoctrine()->getEntityManager();
            $em->merge($place);
            $em->flush();
            return $place;
        }else{
            return $form;
        }
    }
}