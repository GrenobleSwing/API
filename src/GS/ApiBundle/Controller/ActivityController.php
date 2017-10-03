<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\StructureBundle\Entity\Activity;
use GS\StructureBundle\Entity\Topic;
use GS\StructureBundle\Entity\Category;
use GS\StructureBundle\Entity\Discount;
use GS\StructureBundle\Entity\Schedule;

/**
 * @RouteResource("Activity", pluralize=false)
 */
class ActivityController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Returns a form to create a new Activity",
     *   output="GS\StructureBundle\Form\Type\ActivityType",
     *   statusCodes={
     *     200="You have permission to create a Activity, the form is returned",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm(null, 'gs_api_post_activity');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Create a new Activity",
     *   input="GS\StructureBundle\Form\Type\ActivityType",
     *   statusCodes={
     *     201="The Activity has been created",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm(null, 'gs_api_post_activity');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activity = $form->getData();
            $activity->addOwner($this->getUser());
            $year = $activity->getYear();
            $year->addActivity($activity);

            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();

            $view = $this->view(array('id' => $activity->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Returns a form to confirm deletion of a given Activity",
     *   requirements={
     *     {
     *       "name"="activity",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Activity id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Activity, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', activity)")
     */
    public function removeAction(Activity $activity)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($activity, 'activity');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Delete a given Activity",
     *   requirements={
     *     {
     *       "name"="activity",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Activity id"
     *     }
     *   },
     *   input="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Activity has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', activity)")
     */
    public function deleteAction(Activity $activity, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($activity, 'activity');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $year = $activity->getYear();
            $year->removeActivity($activity);

            $em = $this->getDoctrine()->getManager();
            $em->remove($activity);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Returns an existing Activity",
     *   requirements={
     *     {
     *       "name"="activity",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Activity id"
     *     }
     *   },
     *   output="GS\StructureBundle\Entity\Activity",
     *   statusCodes={
     *     200="Returns the Activity",
     *   }
     * )
     * @Security("is_granted('view', activity)")
     */
    public function getAction(Activity $activity)
    {
        $view = $this->view($activity, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Returns a collection of Activitys",
     *   output="array<GS\StructureBundle\Entity\Activity>",
     *   statusCodes={
     *     200="Returns all the Activitys",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listActivities = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Activity')
            ->findAll()
            ;

        $view = $this->view($listActivities, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Returns a form to edit an existing Activity",
     *   requirements={
     *     {
     *       "name"="activity",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Activity id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\ActivityType",
     *   statusCodes={
     *     200="You have permission to create a Activity, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', activity)")
     */
    public function editAction(Activity $activity)
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm($activity, 'gs_api_put_activity', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Update an existing Activity",
     *   input="GS\StructureBundle\Form\Type\ActivityType",
     *   requirements={
     *     {
     *       "name"="activity",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Activity id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Activity has been updated",
     *   }
     * )
     * @Security("is_granted('edit', activity)")
     */
    public function putAction(Activity $activity, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getActivityForm($activity, 'gs_api_put_activity', 'PUT');
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
     *   section="Activity",
     *   description="Returns a form to create a new Category for the given Activity",
     *   output="GS\StructureBundle\Form\Type\CategoryType",
     *   statusCodes={
     *     200="You have permission to create a Category, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', activity)")
     */
    public function newCategoryAction(Activity $activity)
    {
        $category = new Category();
        $category->setActivity($activity);
        $this->denyAccessUnlessGranted('create', $category);
        $form = $this->get('gsapi.form_generator')->getCategoryForm($category, 'gs_api_post_category');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Returns a form to create a new Discount for the given Activity",
     *   output="GS\StructureBundle\Form\Type\DiscountType",
     *   statusCodes={
     *     200="You have permission to create a Discount, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', activity)")
     */
    public function newDiscountAction(Activity $activity)
    {
        $discount = new Discount();
        $discount->setActivity($activity);
        $this->denyAccessUnlessGranted('create', $discount);
        $form = $this->get('gsapi.form_generator')->getDiscountForm($discount, 'gs_api_post_discount');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Activity",
     *   description="Returns a form to create a new Topic for the given Activity",
     *   output="GS\StructureBundle\Form\Type\TopicType",
     *   statusCodes={
     *     200="You have permission to create a Topic, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', activity)")
     */
    public function newTopicAction(Activity $activity)
    {
        $topic = new Topic();
        $topic->setActivity($activity);
        // Add one schedule since it is mandatory to have one.
        $schedule = new Schedule();
        $topic->addSchedule($schedule);
        $this->denyAccessUnlessGranted('create', $topic);
        $form = $this->get('gsapi.form_generator')->getTopicForm($topic, 'gs_api_post_topic');
        $view = $this->get('gsapi.form_generator')->getTopicFormView($form);
        return $this->handleView($view);
    }

}
