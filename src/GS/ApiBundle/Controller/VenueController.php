<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Venue;

/**
 * @RouteResource("Venue", pluralize=false)
 */
class VenueController extends FOSRestController
{

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm(null, 'post_venue');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm(null, 'post_venue');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $venue = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($venue);
            $em->flush();

            $view = $this->view(array('id' => $venue->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', venue)")
     */
    public function removeAction(Venue $venue)
    {
        $form = $this->get('gsapi.form_generator')->getVenueDeleteForm($venue);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', venue)")
     */
    public function deleteAction(Venue $venue, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getVenueDeleteForm($venue);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($venue);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', venue)")
     */
    public function getAction(Venue $venue)
    {
        $view = $this->view($venue, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listVenues = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Venue')
            ->findAll()
            ;

        $view = $this->view($listVenues, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', venue)")
     */
    public function editAction(Venue $venue)
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm($venue, 'put_venue', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', venue)")
     */
    public function putAction(Venue $venue, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getVenueForm($venue, 'put_venue', 'PUT');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $view = $this->view(null, 204);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }
    
}
