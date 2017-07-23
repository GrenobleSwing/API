<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Entity\Registration;

/**
 * @RouteResource("Topic", pluralize=false)
 */
class TopicController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Topic",
     *   description="Create a new Topic",
     *   input="GS\ApiBundle\Form\Type\TopicType",
     *   statusCodes={
     *     201="The Topic has been created",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getTopicForm(null, 'post_topic');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $topic = $form->getData();
            $topic->addOwner($this->getUser());
            $activity = $topic->getActivity();
            $activity->addTopic($topic);

            $em = $this->getDoctrine()->getManager();
            $em->persist($topic);
            $em->flush();

            $view = $this->view(array('id' => $topic->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Topic",
     *   description="Returns a form to confirm deletion of a given Topic",
     *   requirements={
     *     {
     *       "name"="topic",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Topic id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Topic, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', topic)")
     */
    public function removeAction(Topic $topic)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($topic, 'topic');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Topic",
     *   description="Delete a given Topic",
     *   requirements={
     *     {
     *       "name"="topic",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Topic id"
     *     }
     *   },
     *   input="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Topic has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', topic)")
     */
    public function deleteAction(Topic $topic, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($topic, 'topic');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($topic);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Topic",
     *   description="Returns an existing Topic",
     *   requirements={
     *     {
     *       "name"="topic",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Topic id"
     *     }
     *   },
     *   output="GS\ApiBundle\Entity\Topic",
     *   statusCodes={
     *     200="Returns the Topic",
     *   }
     * )
     * @Security("is_granted('view', topic)")
     */
    public function getAction(Topic $topic)
    {
        $view = $this->view($topic, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Topic",
     *   description="Returns a collection of Topics",
     *   output="array<GS\ApiBundle\Entity\Topic>",
     *   statusCodes={
     *     200="Returns all the Topics",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Topic",
     *   description="Returns a form to edit an existing Topic",
     *   requirements={
     *     {
     *       "name"="topic",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Topic id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\TopicType",
     *   statusCodes={
     *     200="You have permission to create a Topic, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', topic)")
     */
    public function editAction(Topic $topic)
    {
        $form = $this->get('gsapi.form_generator')->getTopicForm($topic, 'put_topic', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Topic",
     *   description="Update an existing Topic",
     *   input="GS\ApiBundle\Form\Type\TopicType",
     *   requirements={
     *     {
     *       "name"="topic",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Topic id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Topic has been updated",
     *   }
     * )
     * @Security("is_granted('edit', topic)")
     */
    public function putAction(Topic $topic, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getTopicForm($topic, 'put_topic', 'PUT');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $view = $this->view(null, 204);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Topic",
     *   description="Returns a collection of Registrations attached to a given Topic",
     *   requirements={
     *     {
     *       "name"="topic",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Topic id"
     *     }
     *   },
     *   output="array<GS\ApiBundle\Entity\Registration>",
     *   statusCodes={
     *     200="Returns all the Registrations attached to a given Topic",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Topic",
     *   description="Returns a form to create a new Registration for the given Topic",
     *   input="GS\ApiBundle\Form\Type\RegistrationType",
     *   requirements={
     *     {
     *       "name"="topic",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Topic id"
     *     }
     *   },
     *   statusCodes={
     *     200="You have permission to create an Registration, the form is returned",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function newRegistrationAction(Topic $topic)
    {
        $registration = new Registration();
        $registration->setTopic($topic);
        $this->denyAccessUnlessGranted('create', $registration);
        $registrationService = $this->get('gsapi.registration.service');
        $missingRequirements = $registrationService->checkRequirements($registration, $this->getUser());
        if (count($missingRequirements) > 0) {
            $view = $this->view($missingRequirements, 412);
        }
        else
        {
            $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'post_registration');
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

}
