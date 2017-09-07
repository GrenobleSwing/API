<?php

namespace GS\ApiBundle\Controller;

use GS\ApiBundle\Entity\Invoice;
use GS\ApiBundle\Entity\Payment;
use GS\ApiBundle\Form\Type\PaymentType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{

    /**
     * @Route("/payment/add", name="add_payment")
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function addAction(Request $request)
    {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($payment);

            $repoAccount = $em->getRepository('GSApiBundle:Account');
            $account = $repoAccount->findOneByUser($this->getUser());
            $account->addPayment($payment);

            $repo = $em->getRepository('GSApiBundle:Invoice');
            if ('PAID' == $payment->getState() &&
                    null === $repo->findOneByPayment($payment)) {
                $prefix = $payment->getDate()->format('Y');
                $invoiceNumber = $repo->countByNumber($prefix) + 1;
                $invoice = new Invoice($payment);
                $invoice->setNumber($prefix . sprintf('%05d', $invoiceNumber));
                $invoice->setDate($payment->getDate());

                $this->get('gsapi.payment.service')->sendEmail($payment);

                $em->persist($invoice);
            }
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Paiement bien enregistré.');

            return $this->redirectToRoute('view_payment', array('id' => $payment->getId()));
        }

        return $this->render('GSApiBundle:Payment:add.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/payment/{id}/delete", name="delete_payment", requirements={"id": "\d+"})
     * @Security("is_granted('delete', payment)")
     */
    public function deleteAction(Payment $payment, Request $request)
    {
        if ('PAID' == $payment->getState()) {
            $view = $this->view(null, 403);
            return $this->handleView($view);
        }

        $form = $this->get('gsapi.form_generator')->getDeleteForm($payment, 'payment');
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
     * @Route("/payment/{id}", name="view_payment", requirements={"id": "\d+"})
     * @Security("is_granted('view', payment)")
     */
    public function viewAction(Payment $payment)
    {
        return $this->render('GSApiBundle:Payment:view.html.twig', array(
            'payment' => $payment,
        ));
    }

    /**
     * @Route("/payment", name="index_payment")
     * @Security("has_role('ROLE_TREASURER')")
     */
    public function indexAction()
    {
        $listPayments = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Payment')
            ->findAll()
            ;

        return $this->render('GSApiBundle:Payment:index.html.twig', array(
            'listPayments' => $listPayments
        ));
    }

    /**
     * @Route("/payment/{id}/edit", name="edit_payment", requirements={"id": "\d+"})
     * @Security("is_granted('edit', payment)")
     */
    public function editAction(Payment $payment, Request $request)
    {
        $form = $this->createForm(PaymentType::class, $payment);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $invoice = $em->getRepository('GSApiBundle:Invoice')
                ->findOneByPayment($payment);
            if ('PAID' == $payment->getState() && null === $invoice) {
                $prefix = $payment->getDate()->format('Y');
                $invoiceNumber = $em->getRepository('GSApiBundle:Invoice')
                        ->countByNumber($prefix) + 1;
                $invoice = new Invoice($payment);
                $invoice->setNumber($prefix . sprintf('%05d', $invoiceNumber));
                $invoice->setDate($payment->getDate());

                $this->get('gsapi.payment.service')->sendEmail($payment);

                $em->persist($invoice);
            }
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Paiement bien modifié.');

            return $this->redirectToRoute('view_payment', array('id' => $payment->getId()));
        }

        return $this->render('GSApiBundle:Payment:edit.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

}
