<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Discount;

/**
 * @RouteResource("Discount", pluralize=false)
 */
class DiscountController extends FOSRestController
{

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $discount = $em
            ->getRepository('GSApiBundle:Discount')
            ->find($id)
            ;

        $em->remove($discount);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $discount = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Discount')
            ->find($id)
            ;

        $view = $this->view($discount, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listDiscounts = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Discount')
            ->findAll()
            ;

        $view = $this->view($listDiscounts, 200);
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
        }
        else
        {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }
        
        $em = $this->getDoctrine()->getManager();

        $activity = $em
            ->getRepository('GSApiBundle:Activity')
            ->find($data['activityId']);
        if ($activity === null) {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }
        
        $discount = new Discount();
        $this->setDiscountData($discount, $data);
        $activity->addDiscount($discount);

        $em->persist($activity);
        $em->flush();

        if(null != $discount->getId()) {
            $view = $this->view(array('id' => $discount->getId()), 200);
        }
        else
        {
            $view = $this->view(array(), 301);
        }
        return $this->handleView($view);
    }

    public function putAction($id, Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
        }
        else
        {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }
        
        $em = $this->getDoctrine()->getManager();
        $discount = $em
            ->getRepository('GSApiBundle:Discount')
            ->find($id)
            ;
        $this->setDiscountData($discount, $data);

        $em->flush();
    }
    
    private function setDiscountData(&$discount, $data)
    {
        $discount->setName($data['name']);
        $discount->setType($data['type']);
        $discount->setValue($data['value']);
        $discount->setCondition($data['condition']);
    }
}
