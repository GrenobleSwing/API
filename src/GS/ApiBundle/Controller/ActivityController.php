<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Activity;

/**
 * @RouteResource("Activity", pluralize=false)
 */
class ActivityController extends FOSRestController
{

    public function deleteAction($id)
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Activity')
            ->find($id)
            ;

        $em = $this->getDoctrine()->getManager();
        $em->remove($year);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Activity')
            ->find($id)
            ;

        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listActivities = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Activity')
            ->findAll()
            ;

        $view = $this->view($listActivities, 200);
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

//        $year = $em
//            ->getRepository('GSApiBundle:Year')
//            ->find($data['yearId']);
//        if ($year === null) {
//            $view = $this->view(array(), 301);
//            return $this->handleView($view);
//        }
        
        $activity = new Activity();
        if (! $this->setActivityData($em, $activity, $data)) {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }
//        $this->setActivityData($activity, $data);
//        $year->addActivity($activity);

        $em->persist($activity);
        $em->flush();

        if(null != $activity->getId()) {
            $view = $this->view(array('id' => $activity->getId()), 200);
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
        $activity = $em
            ->getRepository('GSApiBundle:Activity')
            ->find($id)
            ;
        $this->setActivityData($activity, $data);

        $em->flush();
    }
    
    private function setActivityData($em, &$activity, $data)
    {
        $activity->setTitle($data['title']);
        $activity->setDescription($data['description']);
        $activity->setState($data['state']);

        $year = $em
            ->getRepository('GSApiBundle:Year')
            ->find($data['yearId']);
        $year->addActivity($activity);
        if ($year === null) {
            return false;
        }
        return true;
    }
}
