<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\StructureBundle\Entity\Discount;

/**
 * @RouteResource("Discount", pluralize=false)
 */
class DiscountController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Discount",
     *   description="Create a new Discount",
     *   input="GS\StructureBundle\Form\Type\DiscountType",
     *   statusCodes={
     *     201="The Discount has been created",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountForm(null, 'gs_api_post_discount');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $discount = $form->getData();
            $activity = $discount->getActivity();
            $activity->addDiscount($discount);

            $em = $this->getDoctrine()->getManager();
            $em->persist($discount);
            $em->flush();

            $view = $this->view(array('id' => $discount->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Discount",
     *   description="Returns an existing Discount",
     *   requirements={
     *     {
     *       "name"="discount",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Discount id"
     *     }
     *   },
     *   output="GS\StructureBundle\Entity\Discount",
     *   statusCodes={
     *     200="Returns the Discount",
     *   }
     * )
     * @Security("is_granted('view', discount)")
     */
    public function getAction(Discount $discount)
    {
        $view = $this->view($discount, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Discount",
     *   description="Returns a collection of Discounts",
     *   output="array<GS\StructureBundle\Entity\Discount>",
     *   statusCodes={
     *     200="Returns all the Discounts",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function cgetAction()
    {
        $listCategories = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Discount')
            ->findAll()
            ;

        $view = $this->view($listCategories, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Discount",
     *   description="Returns a form to edit an existing Discount",
     *   requirements={
     *     {
     *       "name"="discount",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Discount id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\DiscountType",
     *   statusCodes={
     *     200="You have permission to create a Discount, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', discount)")
     */
    public function editAction(Discount $discount)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountForm($discount, 'gs_api_put_discount', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Discount",
     *   description="Update an existing Discount",
     *   input="GS\StructureBundle\Form\Type\DiscountType",
     *   requirements={
     *     {
     *       "name"="discount",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Discount id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Discount has been updated",
     *   }
     * )
     * @Security("is_granted('edit', discount)")
     */
    public function putAction(Discount $discount, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDiscountForm($discount, 'gs_api_put_discount', 'PUT');
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
     *   section="Discount",
     *   description="Returns a form to confirm deletion of a given Discount",
     *   requirements={
     *     {
     *       "name"="discount",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Discount id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Discount, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', discount)")
     */
    public function removeAction(Discount $discount)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($discount, 'discount');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Discount",
     *   description="Delete a given Discount",
     *   requirements={
     *     {
     *       "name"="discount",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Discount id"
     *     }
     *   },
     *   input="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Discount has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', discount)")
     */
    public function deleteAction(Discount $discount, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($discount, 'discount');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activity = $discount->getActivity();
            $activity->removeDiscount($discount);

            $em = $this->getDoctrine()->getManager();
            $em->remove($discount);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

}
