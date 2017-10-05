<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\StructureBundle\Entity\Year;
use GS\StructureBundle\Entity\Activity;

/**
 * @RouteResource("Year", pluralize=false)
 */
class YearController extends FOSRestController
{
    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns a form to create a new Year",
     *   output="GS\StructureBundle\Form\Type\YearType",
     *   statusCodes={
     *     200="You have permission to create a Year, the form is returned",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getYearForm(null, 'gs_api_post_year');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Create a new Year",
     *   input="GS\StructureBundle\Form\Type\YearType",
     *   statusCodes={
     *     201="The Year has been created",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getYearForm(null, 'gs_api_post_year');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $year = $form->getData();
            $year->addOwner($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($year);
            $em->flush();

            $view = $this->view(array('id' => $year->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns a form to confirm deletion of a given Year",
     *   requirements={
     *     {
     *       "name"="year",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Year id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Year, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', year)")
     */
    public function removeAction(Year $year)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($year, 'year');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Delete a given Year",
     *   requirements={
     *     {
     *       "name"="year",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Year id"
     *     }
     *   },
     *   input="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Year has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', year)")
     */
    public function deleteAction(Year $year, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($year, 'year');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($year);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns current Year based on current date",
     *   output="GS\StructureBundle\Entity\Year",
     *   statusCodes={
     *     200="Returns current Year",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     * @Get("/year/current")
     */
    public function getCurrentAction()
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Year')
            ->findCurrentYear()
            ;
        if ($year === null) {
            $status = 400;
        } else {
            $status = 200;
        }
        $view = $this->view($year, $status);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns next Year based on current date",
     *   output="GS\StructureBundle\Entity\Year",
     *   statusCodes={
     *     200="Returns next Year",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     * @Get("/year/next")
     */
    public function getNextAction()
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Year')
            ->findNextYear()
            ;
        if ($year === null) {
            $status = 400;
        } else {
            $status = 200;
        }
        $view = $this->view($year, $status);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns previous Year based on current date",
     *   output="GS\StructureBundle\Entity\Year",
     *   statusCodes={
     *     200="Returns previous Year",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     * @Get("/year/previous")
     */
    public function getPreviousAction()
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Year')
            ->findPreviousYear()
            ;
        if ($year === null) {
            $status = 400;
        } else {
            $status = 200;
        }
        $view = $this->view($year, $status);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns an existing Year",
     *   requirements={
     *     {
     *       "name"="year",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Year id"
     *     }
     *   },
     *   output="GS\StructureBundle\Entity\Year",
     *   statusCodes={
     *     200="Returns the Year",
     *   }
     * )
     * @Security("is_granted('view', year)")
     */
    public function getAction(Year $year)
    {
        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns a collection of Years",
     *   output="array<GS\StructureBundle\Entity\Year>",
     *   statusCodes={
     *     200="Returns all the Years",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function cgetAction()
    {
        $listYears = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Year')
            ->findAll()
            ;

        $view = $this->view($listYears, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns a form to edit an existing Year",
     *   requirements={
     *     {
     *       "name"="year",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Year id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\YearType",
     *   statusCodes={
     *     200="You have permission to create a Year, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', year)")
     */
    public function editAction(Year $year)
    {
        $form = $this->get('gsapi.form_generator')->getYearForm($year, 'gs_api_put_year', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Update an existing Year",
     *   input="GS\StructureBundle\Form\Type\YearType",
     *   requirements={
     *     {
     *       "name"="year",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Year id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Year has been updated",
     *   }
     * )
     * @Security("is_granted('edit', year)")
     */
    public function putAction(Year $year, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getYearForm($year, 'gs_api_put_year', 'PUT');
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
     *   section="Year",
     *   description="Returns all member Accounts of an existing Year",
     *   requirements={
     *     {
     *       "name"="year",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Year id"
     *     }
     *   },
     *   output="array<GS\StructureBundle\Entity\Account>",
     *   statusCodes={
     *     200="Returns the Year",
     *   }
     * )
     * @Security("has_role('ROLE_SECRETARY')")
     */
    public function getMembersAction(Year $year)
    {
        $members = $this->get('gstoolbox.user.membership')->getMembers($year);
        $view = $this->view($members, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Year",
     *   description="Returns a form to create a new Activity for the given Year",
     *   input="GS\StructureBundle\Form\Type\ActivityType",
     *   requirements={
     *     {
     *       "name"="year",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Year id"
     *     }
     *   },
     *   statusCodes={
     *     200="You have permission to create an Activity, the form is returned",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function newActivityAction(Year $year)
    {
        $activity = new Activity();
        $activity->setYear($year);
        $form = $this->get('gsapi.form_generator')->getActivityForm($activity, 'gs_api_post_activity');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

}
