<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Registration;
use GS\ApiBundle\Entity\Address;

/**
 * @RouteResource("Registration", pluralize=false)
 */
class RegistrationController extends FOSRestController
{

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $registration = $em
            ->getRepository('GSApiBundle:Registration')
            ->find($id)
            ;

        $em->remove($registration);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Registration')
            ->find($id)
            ;

        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listRegistrations = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Registration')
            ->findAll()
            ;

        $view = $this->view($listRegistrations, 200);
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
        $registration = new Registration();
        $view = $this->createUpdateRegistration($em, $registration, $data);
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
        $registration = $em
            ->getRepository('GSApiBundle:Registration')
            ->find($id)
            ;
        $view = $this->createUpdateRegistration($em, $registration, $data);
        return $this->handleView($view);
    }
    
    private function setRegistrationData($em, &$registration, $data)
    {
        $registration->setRole($data['role']);
        
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
        $registration->setAccount($account);
        $registration->setTopic($topic);
        
        if( isset($data['state'])) {
            $registration->setState($data['state']);
        } else {
            $registration->setState('received');
        }
        
        if( null !== $topic->getOptions() &&
                in_array('automatic_validation', $topic->getOptions())) {
            $registration->setState('validated');
        }
        return array();
    }

    private function createUpdateRegistration($em, $registration, $data)
    {
        $errors_json = $this->setRegistrationData($em, $registration, $data);
        $errors_validation = $this->validateRegistration($registration, $errors_json);
        $errors = $this->saveRegistration($em, $registration, $errors_validation);

        if (count($errors) > 0) {
            $view = $this->view($errors, 301);
        } else {
            $view = $this->view(array('id' => $registration->getId()), 200);
        }
        return $view;
    }
    
    private function validateRegistration($registration, $errors)
    {
        if (count($errors) > 0) {
            return $errors;
        } else {
            $validator = $this->get('validator');
            return $validator->validate($registration);
        }
    }

    private function saveRegistration($em, &$registration, $errors)
    {
        if (count($errors) > 0) {
            return $errors;
        } else {
            $em->persist($registration);
            $em->flush();
            if(null === $registration->getId()) {
                return array('message'=> 'Impossible to save registration, retry.');
            } else {
                return array();
            }
        }
    }

}
