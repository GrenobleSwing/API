<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\StructureBundle\Entity\Venue;

/**
 * @RouteResource("Venue", pluralize=false)
 */
class VenueController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Returns a form to create a new Venue",
     *   output="GS\StructureBundle\Form\Type\VenueType",
     *   statusCodes={
     *     200="You have permission to create a Venue, the form is returned",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm(null, 'gs_api_post_venue');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Create a new Venue",
     *   input="GS\StructureBundle\Form\Type\VenueType",
     *   statusCodes={
     *     201="The Venue has been created",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm(null, 'gs_api_post_venue');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $venue = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($venue);
            $em->flush();

            $view = $this->view(array('id' => $venue->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Returns a form to confirm deletion of a given Venue",
     *   requirements={
     *     {
     *       "name"="venue",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Venue id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Venue, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', venue)")
     */
    public function removeAction(Venue $venue)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($venue, 'venue');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Delete a given Venue",
     *   requirements={
     *     {
     *       "name"="venue",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Venue id"
     *     }
     *   },
     *   input="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Venue has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', venue)")
     */
    public function deleteAction(Venue $venue, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($venue, 'venue');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($venue);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Returns an existing Venue",
     *   requirements={
     *     {
     *       "name"="venue",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Venue id"
     *     }
     *   },
     *   output="GS\StructureBundle\Entity\Venue",
     *   statusCodes={
     *     200="Returns the Venue",
     *   }
     * )
     * @Security("is_granted('view', venue)")
     */
    public function getAction(Venue $venue)
    {
        $view = $this->view($venue, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Returns a collection of Venues",
     *   output="array<GS\StructureBundle\Entity\Venue>",
     *   statusCodes={
     *     200="Returns all the Venues",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function cgetAction()
    {
        $listVenues = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Venue')
            ->findAll()
            ;

        $view = $this->view($listVenues, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Returns a form to edit an existing Venue",
     *   requirements={
     *     {
     *       "name"="venue",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Venue id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\VenueType",
     *   statusCodes={
     *     200="You have permission to create a Venue, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', venue)")
     */
    public function editAction(Venue $venue)
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm($venue, 'gs_api_put_venue', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Venue",
     *   description="Update an existing Venue",
     *   input="GS\StructureBundle\Form\Type\VenueType",
     *   requirements={
     *     {
     *       "name"="venue",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Venue id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Venue has been updated",
     *   }
     * )
     * @Security("is_granted('edit', venue)")
     */
    public function putAction(Venue $venue, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm($venue, 'gs_api_put_venue', 'PUT');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $view = $this->view(null, 204);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

}
