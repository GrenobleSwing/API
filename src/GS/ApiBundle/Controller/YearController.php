<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Year;
use GS\ApiBundle\Entity\Activity;

/**
 * @RouteResource("Year", pluralize=false)
 */
class YearController extends FOSRestController
{
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getYearForm(null, 'post_year');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getYearForm(null, 'post_year');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $year = $form->getData();
            $year->addOwner($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($year);
            $em->flush();

            $view = $this->view(array('id' => $year->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', year)")
     */
    public function removeAction(Year $year)
    {
        $form = $this->get('gsapi.form_generator')->getYearDeleteForm($year);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', year)")
     */
    public function deleteAction(Year $year, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getYearDeleteForm($year);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($year);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Get("/year/current")
     */
    public function getCurrentAction()
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Year')
            ->findCurrentYear()
            ;
        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Get("/year/next")
     */
    public function getNextAction()
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Year')
            ->findNextYear()
            ;
        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     * @Get("/year/previous")
     */
    public function getPreviousAction()
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Year')
            ->findPreviousYear()
            ;
        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', year)")
     */
    public function getAction(Year $year)
    {
        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listYears = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Year')
            ->findAll()
            ;

        $view = $this->view($listYears, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', year)")
     */
    public function editAction(Year $year)
    {
        $form = $this->get('gsapi.form_generator')->getYearForm($year, 'put_year', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', year)")
     */
    public function putAction(Year $year, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getYearForm($year, 'put_year', 'PUT');
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
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newActivityAction(Year $year)
    {
        $activity = new Activity();
        $activity->setYear($year);
        $form = $this->get('gsapi.form_generator')->getActivityForm($activity, 'post_activity');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

}
