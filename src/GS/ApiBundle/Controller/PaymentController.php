<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Invoice;
use GS\ApiBundle\Entity\Payment;

/**
 * @RouteResource("Payment", pluralize=false)
 */
class PaymentController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Returns a form to create a new Payment",
     *   output="GS\ApiBundle\Form\Type\PaymentType",
     *   statusCodes={
     *     200="You have permission to create a Payment, the form is returned",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Payment",
     *   description="Create a new Payment",
     *   input="GS\ApiBundle\Form\Type\PaymentType",
     *   statusCodes={
     *     201="The Payment has been created",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm(null, 'post_payment');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payment = $form->getData();
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);
            
            $repo = $em->getRepository('GSApiBundle:Invoice');
            if ('PAID' == $payment->getState() &&
                    null === $repo->findOneByPayment($payment)) {
                $prefix = $payment->getDate()->format('Y');
                $invoiceNumber = $repo->countByNumber($prefix) + 1;
                $invoice = new Invoice($payment);
                $invoice->setNumber($prefix . sprintf('%05d', $invoiceNumber));
                $invoice->setDate($payment->getDate());
                $em->persist($invoice);
            }
            $em->flush();

            $view = $this->view(array('id' => $payment->getId()), 201);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Returns a form to confirm deletion of a given Payment",
     *   requirements={
     *     {
     *       "name"="payment",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Payment id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Payment, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', payment)")
     */
    public function removeAction(Payment $payment)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($payment, 'payment');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Delete a given Payment",
     *   requirements={
     *     {
     *       "name"="payment",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Payment id"
     *     }
     *   },
     *   input="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Payment has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', payment)")
     */
    public function deleteAction(Payment $payment, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($payment, 'payment');
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
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
     * @ApiDoc(
     *   section="Payment",
     *   description="Returns an existing Payment",
     *   requirements={
     *     {
     *       "name"="payment",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Payment id"
     *     }
     *   },
     *   output="GS\ApiBundle\Entity\Payment",
     *   statusCodes={
     *     200="Returns the Payment",
     *   }
     * )
     * @Security("is_granted('view', payment)")
     */
    public function getAction(Payment $payment)
    {
        $view = $this->view($payment, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Returns a collection of Payments",
     *   output="array<GS\ApiBundle\Entity\Payment>",
     *   statusCodes={
     *     200="Returns all the Payments",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Payment",
     *   description="Returns a form to edit an existing Payment",
     *   requirements={
     *     {
     *       "name"="payment",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Payment id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\PaymentType",
     *   statusCodes={
     *     200="You have permission to create a Payment, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', payment)")
     */
    public function editAction(Payment $payment)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm($payment, 'put_payment', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Update an existing Payment",
     *   input="GS\ApiBundle\Form\Type\PaymentType",
     *   requirements={
     *     {
     *       "name"="payment",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Payment id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Payment has been updated",
     *   }
     * )
     * @Security("is_granted('edit', payment)")
     */
    public function putAction(Payment $payment, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm($payment, 'put_payment', 'PUT');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            if ('PAID' == $payment->getState()) {
                $prefix = $payment->getDate()->format('Y');
                $invoiceNumber = $em->getRepository('GSApiBundle:Invoice')
                        ->countByNumber($prefix) + 1;
                $invoice = new Invoice($payment);
                $invoice->setNumber($prefix . sprintf('%05d', $invoiceNumber));
                $invoice->setDate($payment->getDate());
                $em->persist($invoice);
            }
            $em->flush();

            $view = $this->view(null, 204);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }
    
}
