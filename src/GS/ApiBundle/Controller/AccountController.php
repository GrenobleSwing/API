<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\Address;

/**
 * @RouteResource("Account", pluralize=false)
 */
class AccountController extends FOSRestController
{

    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $account = $em
            ->getRepository('GSApiBundle:Account')
            ->find($id)
            ;

        $em->remove($account);
        $em->flush();

        $view = $this->view(array(), 200);
        return $this->handleView($view);
    }

    public function getAction($id)
    {
        $year = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->find($id)
            ;

        $view = $this->view($year, 200);
        return $this->handleView($view);
    }

    public function cgetAction()
    {
        $listAccounts = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->findAll()
            ;

        $view = $this->view($listAccounts, 200);
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

        $account = new Account();
        if (! $this->setAccountData($em, $account, $data)) {
            $view = $this->view(array(), 301);
            return $this->handleView($view);
        }

        $em->persist($account);
        $em->flush();

        if(null != $account->getId()) {
            $view = $this->view(array('id' => $account->getId()), 200);
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
        $account = $em
            ->getRepository('GSApiBundle:Account')
            ->find($id)
            ;
        $this->setAccountData($account, $data);

        $em->flush();
    }
    
    private function setAccountData($em, &$account, $data)
    {
        $account->setFirstName($data['firstName']);
        $account->setLastName($data['lastName']);
        $account->setBirthDate(new \DateTime($data['birthDate']));
        
        $phoneNumber = $this->container
                ->get('libphonenumber.phone_number_util')
                ->parse($data['phoneNumber'], "FR");
        $account->setPhoneNumber($phoneNumber);

        $address = new Address();
        $account->setAddress($address);

        $user = $em
            ->getRepository('GSApiBundle:User')
            ->find($data['userId']);
        if ($user === null) {
            return false;
        }
        $account->setUser($user);
        $account->setEmail($user->getEmail());
        return true;
    }
}
