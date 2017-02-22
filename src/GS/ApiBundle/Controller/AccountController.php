<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\Address;

/**
 * @RouteResource("Account", pluralize=false)
 */
class AccountController extends FOSRestController
{

    /**
     * @Delete("/account/{id}")
     */
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

    /**
     * @Get("/account/{id}")
     */
    public function getAction($id)
    {
        $account = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->find($id)
            ;

        $view = $this->view($account, 200);
        return $this->handleView($view);
    }

    /**
     * @Get("/account")
     */
    public function cgetAction()
    {
        $listAccounts = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->findAll()
            ;

        $view = $this->view($listAccounts, 200);
        return $this->handleView($view);
    }

    /**
     * @Post("/account")
     */
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

    /**
     * @Put("/account/{id}")
     */
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

    /**
     * @Get("/account/{id}/balance")
     */
    public function getBalanceAction($id)
    {
        $account = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->find($id);
        
        $registrations = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Registration')
            ->getValidatedRegistrationsForAccount($account);

        $balance = array();
        $totalDue = 0.0;
        for ($i = 0; $i < count($registrations); $i++) {
            $registration = $registrations[$i];
            $line = $this->getPriceToPay($i, $account, $registration);
            $balance[] = $line;
            $totalDue += $line['due'];
        }

        $view = $this->view(array(
            'details' => $balance,
            'totalDue' => $totalDue,
                ), 200);
        return $this->handleView($view);
    }

    private function getPriceToPay($i, $account, $registration)
    {
        $topic = $registration->getTopic();
        $category = $topic->getCategory();
        $discounts = $category->getDiscounts();
        $discount = $this->applyDiscounts($i, $account, $discounts);
        $price = $category->getPrice();
        $line = array(
            'registrationId' => $registration->getId(),
            'name' => $topic->getTitle(),
            'description' => $topic->getDescription(),
            'price' => $price,
        );
        $due = $price;
        if (null !== $discount) {
            $line['discount'] = array(
                'type' => $discount->getType(),
                'value' => $discount->getValue(),
            );
            if($discount->getType() == 'percent') {
                $due *= 1 - $discount->getValue() / 100;
            } else {
                $due -= $discount->getValue();
            }
        }
        $line['due'] = $due;
        return $line;
    }
    
    private function applyDiscounts($i, $account, $discounts)
    {
        foreach($discounts as $discount) {
            if($i >= 4 && $discount->getCondition() == '5th') {
                return $discount;
            } elseif($i >= 3 && $discount->getCondition() == '4th') {
                return $discount;
            } elseif($i >= 2 && $discount->getCondition() == '3rd') {
                return $discount;
            } elseif($i >= 1 && $discount->getCondition() == '2nd') {
                return $discount;
            } elseif($account->isStudent() && $discount->getCondition() == 'student') {
                return $discount;
            } elseif($account->isMember() && $discount->getCondition() == 'member') {
                return $discount;
            }
        }
        return null;
    }

}
