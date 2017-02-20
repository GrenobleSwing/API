<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Entity\Address;

/**
 * @RouteResource("Topic", pluralize=false)
 */
class TopicController extends FOSRestController
{

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        $topic = $em
            ->getRepository('GSApiBundle:Topic')
            ->find($id)
            ;

        $em->remove($topic);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $topic = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Topic')
            ->find($id)
            ;

        $view = $this->view($topic, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listTopics = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Topic')
            ->findAll()
            ;

        $view = $this->view($listTopics, 200);
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

        $topic = new Topic();
        if(! $this->setTopicData($em, $topic, $data))
        {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }

        $em->persist($topic);
        $em->flush();

        if(null != $topic->getId()) {
            $view = $this->view(array('id' => $topic->getId()), 200);
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
        $topic = $em
            ->getRepository('GSApiBundle:Topic')
            ->find($id)
            ;
        if(! $this->setTopicData($em, $topic, $data))
        {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }

        $em->flush();
    }
    
    private function setTopicData($em, &$topic, $data)
    {
        $topic->setTitle($data['title']);
        $topic->setDescription($data['description']);
        $topic->setState($data['state']);

        // $days = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'); 
        $topic->setDay($data['day']);
        
        $topic->setStartTime(new \DateTime($data['startTime']));
        $topic->setEndTime(new \DateTime($data['endTime']));

        $address = new Address();
        $topic->setAddress($address);
        
        $category = $em
            ->getRepository('GSApiBundle:Category')
            ->find($data['categoryId']);
        $activity = $em
            ->getRepository('GSApiBundle:Activity')
            ->find($data['activityId']);
        if ($activity === null || $category === null) {
            return false;
        }
        $activity->addTopic($topic);
        $topic->setCategory($category);
        return true;
    }
}
