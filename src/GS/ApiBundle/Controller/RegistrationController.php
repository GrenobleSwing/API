<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Registration;

/**
 * @RouteResource("Registration", pluralize=false)
 */
class RegistrationController extends FOSRestController
{
    
    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to VALIDATED",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been validated",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to WAITING",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been put in waiting list",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to CANCELLED",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been cancelled",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to PAID",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been paid",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Registration",
     *   description="Create a new Registration",
     *   input="GS\ApiBundle\Form\Type\RegistrationType",
     *   statusCodes={
     *     201="The Registration has been created",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns a form to confirm deletion of a given Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Registration, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', registration)")
     */
    public function removeAction(Registration $registration)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($registration, 'registration');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Delete a given Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   input="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Registration has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', registration)")
     */
    public function deleteAction(Registration $registration, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($registration, 'registration');
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
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns an existing Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   output="GS\ApiBundle\Entity\Registration",
     *   statusCodes={
     *     200="Returns the Registration",
     *   }
     * )
     * @Security("is_granted('view', registration)")
     */
    public function getAction(Registration $registration)
    {
        $view = $this->view($registration, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns a collection of Registrations",
     *   output="array<GS\ApiBundle\Entity\Registration>",
     *   statusCodes={
     *     200="Returns all the Registrations",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns a form to edit an existing Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\RegistrationType",
     *   statusCodes={
     *     200="You have permission to create a Registration, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', registration)")
     */
    public function editAction(Registration $registration)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'put_registration', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Update an existing Registration",
     *   input="GS\ApiBundle\Form\Type\RegistrationType",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Registration has been updated",
     *   }
     * )
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
