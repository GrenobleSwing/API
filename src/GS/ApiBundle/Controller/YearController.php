<?php

namespace GS\ApiBundle\Controller;

//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Year;

/**
 * @RouteResource("Year", pluralize=false)
 */
class YearController extends FOSRestController
{

    public function deleteAction($id)
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Year')
            ->find($id)
            ;

        $em = $this->getDoctrine()->getManager();
        $em->remove($year);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Year')
            ->find($id)
            ;

        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listYears = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Year')
            ->findAll()
            ;

        $view = $this->view($listYears, 200);
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
        
        $year = new Year();
        $this->setYearData($year, $data);

        $em = $this->getDoctrine()->getManager();
        $em->persist($year);
        $em->flush();

        if(null != $year->getId()) {
            $view = $this->view(array('id' => $year->getId()), 200);
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
        $year = $em
            ->getRepository('GSApiBundle:Year')
            ->find($id)
            ;
        $this->setYearData($year, $data);

        $em->flush();
    }
    
    private function setYearData(&$year, $data)
    {
        $year->setTitle($data['title']);
        $year->setDescription($data['description']);
        $year->setStartDate(new \DateTime($data['startDate']));
        $year->setEndDate(new \DateTime($data['endDate']));
        $year->setState($data['state']);        
    }
}
