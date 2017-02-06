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
use Doctrine\ORM\QueryBuilder;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PlaceController extends Controller
{


    /**
     *
     * @ApiDoc(
     *     description="Get list of places",
     *     output={ "class"=Place::class, "collection"=true, "groups"={"place"} }
     * )
     *
     *
     *
     * @QueryParam(name="offset", requirements="\d+", default="")
     * @QueryParam(name="limit", requirements="\d+", default="")
     * @QueryParam(name="sort", requirements="asc|desc", nullable=true)
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Get("/places")
     * @param Request $request
     */
    public function getPlacesAction(Request $request, ParamFetcher $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $sort = $paramFetcher->get('sort');

        $qb = $this->get('doctrine.orm.entity_manager')->createQueryBuilder();
        $qb->select('p')->from('AppBundle:Place', 'p');

        if($offset != ""){
            $qb->setFirstResult($offset);
        }

        if($limit != ""){
            $qb->setMaxResults($limit);
        }
        if($sort != ""){
            $qb->orderBy('p.name', $sort);
        }

        $places = $qb->getQuery()->getResult();

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
     * @ApiDoc(
     *     resource=true,
     *     description="Create a new place",
     *     input={ "class"=PlaceType::class, "name"="" },
     *     statusCodes={
     *          201 = "Place created with success",
     *          400 = "Errors in the form"
     *     },
     *     responseMap={
     *          201 = {"class"=Place::class, "groups"={"place"}},
     *          400 = {"class"=PlaceType::class, "form_errors"=true, "name"="" }
     *     }
     * )
     *
     *
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