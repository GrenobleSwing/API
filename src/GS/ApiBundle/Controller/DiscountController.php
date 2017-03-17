<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Discount;

/**
 * @RouteResource("Discount", pluralize=false)
 */
class DiscountController extends FOSRestController
{
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountForm(null, 'post_discount');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $discount = $form->getData();
            $activity = $discount->getActivity();
            $activity->addDiscount($discount);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($discount);
            $em->flush();

            $view = $this->view(array('id' => $discount->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', discount)")
     */
    public function getAction(Discount $discount)
    {
        $view = $this->view($discount, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listCategories = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Discount')
            ->findAll()
            ;

        $view = $this->view($listCategories, 200);
        return $this->handleView($view);
    }
    
    /**
     * @Security("is_granted('edit', discount)")
     */
    public function editAction(Discount $discount)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountForm($discount, 'put_discount', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', discount)")
     */
    public function putAction(Discount $discount, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountForm($discount, 'put_discount', 'PUT');
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
     * @Security("is_granted('delete', discount)")
     */
    public function removeAction(Discount $discount)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountDeleteForm($discount);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', discount)")
     */
    public function deleteAction(Discount $discount, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountDeleteForm($discount);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $activity = $discount->getActivity();
            $activity->removeDiscount($discount);

            $em = $this->getDoctrine()->getManager();
            $em->remove($discount);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

}
