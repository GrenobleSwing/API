<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Subscription;
use GS\ApiBundle\Entity\Address;

/**
 * @RouteResource("Subscription", pluralize=false)
 */
class SubscriptionController extends FOSRestController
{

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $subscription = $em
            ->getRepository('GSApiBundle:Subscription')
            ->find($id)
            ;

        $em->remove($subscription);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Subscription')
            ->find($id)
            ;

        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listSubscriptions = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Subscription')
            ->findAll()
            ;

        $view = $this->view($listSubscriptions, 200);
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
        } else {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }
        
        $em = $this->getDoctrine()->getManager();
        $subscription = new Subscription();
        $view = $this->createUpdateSubscription($em, $subscription, $data);
        return $this->handleView($view);
    }

    public function putAction($id, Request $request)
    {
        if (0 === strpos($request->headers->get('Content-Type'), 'application/json')) {
            $data = json_decode($request->getContent(), true);
        } else {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }
        
        $em = $this->getDoctrine()->getManager();
        $subscription = $em
            ->getRepository('GSApiBundle:Subscription')
            ->find($id)
            ;
        $view = $this->createUpdateSubscription($em, $subscription, $data);
        return $this->handleView($view);
    }
    
    private function setSubscriptionData($em, &$subscription, $data)
    {
        $subscription->setRole($data['role']);
        $subscription->setState($data['state']);
        
        $account = $em
            ->getRepository('GSApiBundle:Account')
            ->find($data['accountId']);
        if ($account === null) {
            return array('message'=> 'Account is not good.');
        }
        $topic = $em
            ->getRepository('GSApiBundle:Topic')
            ->find($data['topicId']);
        if ($topic === null) {
            return array('message'=> 'Topic is not good.');
        }
        $subscription->setAccount($account);
        $subscription->setTopic($topic);
        return array();
    }

    private function createUpdateSubscription($em, $subscription, $data)
    {
        $errors_json = $this->setSubscriptionData($em, $subscription, $data);
        $errors_validation = $this->validateSubscription($subscription, $errors_json);
        $errors = $this->saveSubscription($em, $subscription, $errors_validation);

        if (count($errors) > 0) {
            $view = $this->view($errors, 301);
        } else {
            $view = $this->view(array('id' => $subscription->getId()), 200);
        }
        return $view;
    }
    
    private function validateSubscription($subscription, $errors)
    {
        if (count($errors) > 0) {
            return $errors;
        } else {
            $validator = $this->get('validator');
            return $validator->validate($subscription);
        }
    }

    private function saveSubscription($em, &$subscription, $errors)
    {
        if (count($errors) > 0) {
            return $errors;
        } else {
            $em->persist($subscription);
            $em->flush();
            if(null === $subscription->getId()) {
                return array('message'=> 'Impossible to save subscription, retry.');
            } else {
                return array();
            }
        }
    }

}
