<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Category;

/**
 * @RouteResource("Category", pluralize=false)
 */
class CategoryController extends FOSRestController
{

    /**
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryForm(null, 'post_category');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $category = $form->getData();
            $activity = $category->getActivity();
            $activity->addCategory($category);
            
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            $view = $this->view(array('id' => $category->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', category)")
     */
    public function getAction(Category $category)
    {
        $view = $this->view($category, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
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
     * @Security("is_granted('edit', category)")
     */
    public function editAction(Category $category)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryForm($category, 'put_category', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', category)")
     */
    public function putAction(Category $category, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryForm($category, 'put_category', 'PUT');
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
     * @Security("is_granted('delete', category)")
     */
    public function removeAction(Category $category)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryDeleteForm($category);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', category)")
     */
    public function deleteAction(Category $category, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getCategoryDeleteForm($category);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $activity = $category->getActivity();
            $activity->removeCategory($category);

            $em = $this->getDoctrine()->getManager();
            $em->remove($category);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

}
