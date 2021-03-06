<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\StructureBundle\Entity\Invoice;
use GS\StructureBundle\Entity\Payment;

/**
 * @RouteResource("Payment", pluralize=false)
 */
class PaymentController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Returns a form to create a new Payment",
     *   output="GS\StructureBundle\Form\Type\PaymentType",
     *   statusCodes={
     *     200="You have permission to create a Payment, the form is returned",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm(null, 'gs_api_post_payment');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Create a new Payment",
     *   input="GS\StructureBundle\Form\Type\PaymentType",
     *   statusCodes={
     *     201="The Payment has been created",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm(null, 'gs_api_post_payment');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payment = $form->getData();

            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);

            $repoAccount = $em->getRepository('GSStructureBundle:Account');
            $account = $repoAccount->findOneByUser($this->getUser());
            $account->addPayment($payment);

            $repo = $em->getRepository('GSStructureBundle:Invoice');
            if ('PAID' == $payment->getState() &&
                    null === $repo->findOneByPayment($payment)) {
                $prefix = $payment->getDate()->format('Y');
                $invoiceNumber = $repo->countByNumber($prefix) + 1;
                $invoice = new Invoice($payment);
                $invoice->setNumber($prefix . sprintf('%05d', $invoiceNumber));
                $invoice->setDate($payment->getDate());

                $this->get('gstoolbox.payment.service')->sendEmailSuccess($payment);

                $em->persist($invoice);
            }
            $em->flush();

            $view = $this->view(array('id' => $payment->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
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
     *   output="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Payment, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', payment)")
     */
    public function removeAction(Payment $payment)
    {
        if ('PAID' == $payment->getState() || 'IN_PRO' == $payment->getState()) {
            $view = $this->view(null, 403);
            return $this->handleView($view);
        }
        $form = $this->get('gsapi.form_generator')->getDeleteForm($payment, 'gs_api_payment');
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
     *   input="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Payment has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', payment)")
     */
    public function deleteAction(Payment $payment, Request $request)
    {
        if ('PAID' == $payment->getState() || 'IN_PRO' == $payment->getState()) {
            $view = $this->view(null, 403);
            return $this->handleView($view);
        }

        $form = $this->get('gsapi.form_generator')->getDeleteForm($payment, 'gs_api_payment');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $payment->getAccount()->removePayment($payment);
            $em = $this->getDoctrine()->getManager();
            $em->remove($payment);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form, 412);
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
     *   output="GS\StructureBundle\Entity\Payment",
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
     *   output="array<GS\StructureBundle\Entity\Payment>",
     *   statusCodes={
     *     200="Returns all the Payments",
     *   }
     * )
     * @Security("has_role('ROLE_TREASURER')")
     */
    public function cgetAction()
    {
        $listPayments = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Payment')
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
     *   output="GS\StructureBundle\Form\Type\PaymentType",
     *   statusCodes={
     *     200="You have permission to create a Payment, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', payment)")
     */
    public function editAction(Payment $payment)
    {
        $form = $this->get('gsapi.form_generator')->getPaymentForm($payment, 'gs_api_put_payment', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Payment",
     *   description="Update an existing Payment",
     *   input="GS\StructureBundle\Form\Type\PaymentType",
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
        $form = $this->get('gsapi.form_generator')->getPaymentForm($payment, 'gs_api_put_payment', 'PUT');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $invoice = $em->getRepository('GSStructureBundle:Invoice')
                ->findOneByPayment($payment);
            if ('PAID' == $payment->getState() && null === $invoice) {
                $prefix = $payment->getDate()->format('Y');
                $invoiceNumber = $em->getRepository('GSStructureBundle:Invoice')
                        ->countByNumber($prefix) + 1;
                $invoice = new Invoice($payment);
                $invoice->setNumber($prefix . sprintf('%05d', $invoiceNumber));
                $invoice->setDate($payment->getDate());

                $this->get('gstoolbox.payment.service')->sendEmailSuccess($payment);

                $em->persist($invoice);
            }
            $em->flush();

            $view = $this->view(null, 204);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

}
