<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Entity\Registration;

/**
 * @RouteResource("Topic", pluralize=false)
 */
class TopicController extends FOSRestController
{

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getTopicForm(null, 'post_topic');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $topic = $form->getData();
            $topic->addOwner($this->getUser());
            $activity = $topic->getActivity();
            $activity->addTopic($topic);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($topic);
            $em->flush();

            $view = $this->view(array('id' => $topic->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', topic)")
     */
    public function removeAction(Topic $topic)
    {
        $form = $this->get('gsapi.form_generator')->getTopicDeleteForm($topic);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', topic)")
     */
    public function deleteAction(Topic $topic, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getTopicDeleteForm($topic);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($topic);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', topic)")
     */
    public function getAction(Topic $topic)
    {
        $view = $this->view($topic, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listTopics = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Topic')
            ->findAll()
            ;

        $view = $this->view($listTopics, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', topic)")
     */
    public function editAction(Topic $topic)
    {
        $form = $this->get('gsapi.form_generator')->getTopicForm($topic, 'put_topic', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', topic)")
     */
    public function putAction(Topic $topic, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getTopicForm($topic, 'put_topic', 'PUT');
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
     * @Security("is_granted('view', topic)")
     */
    public function getRegistrationsAction(Topic $topic)
    {
        $registrations = $this->getDoctrine()->getManager()
                ->getRepository('GSApiBundle:Registration')
                ->findBy(array('topic' => $topic))
                ;
        
        $view = $this->view($registrations, 200);
        return $this->handleView($view);
    }
    
    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newRegistrationAction(Topic $topic)
    {
        $registration = new Registration();
        $registration->setTopic($topic);
        $this->denyAccessUnlessGranted('create', $registration);
        $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'post_registration');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

}
