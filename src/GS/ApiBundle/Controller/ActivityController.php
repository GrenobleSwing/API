<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Entity\Category;
use GS\ApiBundle\Entity\Discount;
use GS\ApiBundle\Entity\Schedule;

/**
 * @RouteResource("Activity", pluralize=false)
 */
class ActivityController extends FOSRestController
{

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm(null, 'post_activity');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm(null, 'post_activity');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $activity = $form->getData();
            $activity->addOwner($this->getUser());
            $year = $activity->getYear();
            $year->addActivity($activity);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();

            $view = $this->view(array('id' => $activity->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', activity)")
     */
    public function removeAction(Activity $activity)
    {
        $form = $this->get('gsapi.form_generator')->getActivityDeleteForm($activity);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', activity)")
     */
    public function deleteAction(Activity $activity, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getActivityDeleteForm($activity);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $year = $activity->getYear();
            $year->removeActivity($activity);

            $em = $this->getDoctrine()->getManager();
            $em->remove($activity);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', activity)")
     */
    public function getAction(Activity $activity)
    {
        $view = $this->view($activity, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listActivities = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Activity')
            ->findAll()
            ;

        $view = $this->view($listActivities, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', activity)")
     */
    public function editAction(Activity $activity)
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm($activity, 'put_activity', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', activity)")
     */
    public function putAction(Activity $activity, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm($activity, 'put_activity', 'PUT');
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
     * @Security("has_role('ROLE_USER')")
     */
    public function newCategoryAction(Activity $activity)
    {
        $category = new Category();
        $category->setActivity($activity);
        $this->denyAccessUnlessGranted('create', $category);
        $form = $this->get('gsapi.form_generator')->getCategoryForm($category, 'post_category');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newDiscountAction(Activity $activity)
    {
        $discount = new Discount();
        $discount->setActivity($activity);
        $this->denyAccessUnlessGranted('create', $discount);
        $form = $this->get('gsapi.form_generator')->getDiscountForm($discount, 'post_discount');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function newTopicAction(Activity $activity)
    {
        $topic = new Topic();
        $topic->setActivity($activity);
        // Add one schedule since it is mandatory to have one.
        $schedule = new Schedule();
        $topic->addSchedule($schedule);
        $this->denyAccessUnlessGranted('create', $topic);
        $form = $this->get('gsapi.form_generator')->getTopicForm($topic, 'post_topic');
        $view = $this->get('gsapi.form_generator')->getTopicFormView($form);
        return $this->handleView($view);
    }

}
