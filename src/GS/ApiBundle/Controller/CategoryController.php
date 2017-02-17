<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Category;

/**
 * @RouteResource("Category", pluralize=false)
 */
class CategoryController extends FOSRestController
{

    public function deleteAction($id)
    {
        $category = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Category')
            ->find($id)
            ;

        $em = $this->getDoctrine()->getManager();
        $em->remove($category);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $category = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Category')
            ->find($id)
            ;

        $view = $this->view($category, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listCategories = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Category')
            ->findAll()
            ;

        $view = $this->view($listCategories, 200);
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

        $activity = $em
            ->getRepository('GSApiBundle:Activity')
            ->find($data['activityId']);
        if ($activity === null) {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }
        
        $category = new Category();
        $this->setCategoryData($category, $data);
        $activity->addCategory($category);

        $em->persist($activity);
        $em->flush();

        if(null != $category->getId()) {
            $view = $this->view(array('id' => $category->getId()), 200);
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
        $category = $em
            ->getRepository('GSApiBundle:Category')
            ->find($id)
            ;
        $this->setCategoryData($category, $data);

        $em->flush();
    }
    
    private function setCategoryData(&$category, $data)
    {
        $category->setName($data['name']);
        $category->setPrice($data['price']);
    }
}
