<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Registration;

/**
 * @RouteResource("Registration", pluralize=false)
 */
class RegistrationController extends FOSRestController
{
    
    /**
     * @Security("is_granted('validate', registration)")
     * @Get("/registration/{id}/validate")
     */
    public function validateAction(Registration $registration)
    {
        $registration->validate();
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('wait', registration)")
     * @Get("/registration/{id}/wait")
     */
    public function waitAction(Registration $registration)
    {
        $registration->wait();
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('cancel', registration)")
     * @Get("/registration/{id}/cancel")
     */
    public function cancelAction(Registration $registration)
    {
        $registration->cancel();
        
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('pay', registration)")
     * @Get("/registration/{id}/pay")
     */
    public function payAction(Registration $registration)
    {
        $registration->pay();
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationForm(null, 'post_registration');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData();
            
            $account = $this->getDoctrine()
                    ->getRepository('GSApiBundle:Account')
                    ->findOneByUser($this->getUser());
            $registration->setAccount($account);

            $topic = $registration->getTopic();
            $topic->addRegistration($registration);
            
            $registration->setState('SUBMITTED');
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($registration);
            $em->flush();

            $view = $this->view(array('id' => $registration->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', registration)")
     */
    public function removeAction(Registration $registration)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationDeleteForm($registration);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', registration)")
     */
    public function deleteAction(Registration $registration, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationDeleteForm($registration);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($registration);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', registration)")
     */
    public function getAction(Registration $registration)
    {
        $view = $this->view($registration, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listRegistrations = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Registration')
            ->findAll()
            ;

        $view = $this->view($listRegistrations, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', registration)")
     */
    public function editAction(Registration $registration)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'put_registration', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', registration)")
     */
    public function putAction(Registration $registration, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'put_registration', 'PUT');
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
