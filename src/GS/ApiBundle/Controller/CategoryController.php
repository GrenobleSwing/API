<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Category;

/**
 * @RouteResource("Category", pluralize=false)
 */
class CategoryController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Category",
     *   description="Create a new Category",
     *   input="GS\ApiBundle\Form\Type\CategoryType",
     *   statusCodes={
     *     201="The Category has been created",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryForm(null, 'gs_api_post_category');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $category = $form->getData();
            $activity = $category->getActivity();
            $activity->addCategory($category);

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $view = $this->view(array('id' => $category->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Category",
     *   description="Returns an existing Category",
     *   requirements={
     *     {
     *       "name"="category",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Category id"
     *     }
     *   },
     *   output="GS\ApiBundle\Entity\Category",
     *   statusCodes={
     *     200="Returns the Category",
     *   }
     * )
     * @Security("is_granted('view', category)")
     */
    public function getAction(Category $category)
    {
        $view = $this->view($category, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Category",
     *   description="Returns a collection of Categorys",
     *   output="array<GS\ApiBundle\Entity\Category>",
     *   statusCodes={
     *     200="Returns all the Categorys",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function cgetAction()
    {
        $listCategories = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Category')
            ->findAll()
            ;

        $view = $this->view($listCategories, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Category",
     *   description="Returns a form to edit an existing Category",
     *   requirements={
     *     {
     *       "name"="category",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Category id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\CategoryType",
     *   statusCodes={
     *     200="You have permission to create a Category, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', category)")
     */
    public function editAction(Category $category)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryForm($category, 'gs_api_put_category', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Category",
     *   description="Update an existing Category",
     *   input="GS\ApiBundle\Form\Type\CategoryType",
     *   requirements={
     *     {
     *       "name"="category",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Category id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Category has been updated",
     *   }
     * )
     * @Security("is_granted('edit', category)")
     */
    public function putAction(Category $category, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryForm($category, 'gs_api_put_category', 'PUT');
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
     *   section="Category",
     *   description="Returns a form to confirm deletion of a given Category",
     *   requirements={
     *     {
     *       "name"="category",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Category id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Category, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', category)")
     */
    public function removeAction(Category $category)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($category, 'category');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Category",
     *   description="Delete a given Category",
     *   requirements={
     *     {
     *       "name"="category",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Category id"
     *     }
     *   },
     *   input="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Category has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', category)")
     */
    public function deleteAction(Category $category, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($category, 'category');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $activity = $category->getActivity();
            $activity->removeCategory($category);

            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

}
