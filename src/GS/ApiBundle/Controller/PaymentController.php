<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Payment;

/**
 * @RouteResource("Payment", pluralize=false)
 */
class PaymentController extends FOSRestController
{

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm(null, 'post_payment');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm(null, 'post_payment');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $payment = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            $em->flush();

            $view = $this->view(array('id' => $payment->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', payment)")
     */
    public function removeAction(Payment $payment)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentDeleteForm($payment);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', payment)")
     */
    public function deleteAction(Payment $payment, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentDeleteForm($payment);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($payment);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', payment)")
     */
    public function getAction(Payment $payment)
    {
        $view = $this->view($payment, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_TREASURER')")
     */
    public function cgetAction()
    {
        $listPayments = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Payment')
            ->findAll()
            ;

        $view = $this->view($listPayments, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', payment)")
     */
    public function editAction(Payment $payment)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm($payment, 'put_payment', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', payment)")
     */
    public function putAction(Payment $payment, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm($payment, 'put_payment', 'PUT');
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
