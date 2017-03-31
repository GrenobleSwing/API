<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Account;

/**
 * @RouteResource("Account", pluralize=false)
 */
class AccountController extends FOSRestController
{

    /**
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm(null, 'post_account');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm(null, 'post_account');
        $form->handleRequest($request);

        if ($form->isValid()) {
            $account = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            $view = $this->view(array('id' => $account->getId()), 200);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', account)")
     */
    public function removeAction(Account $account)
    {
        $form = $this->get('gsapi.form_generator')->getAccountDeleteForm($account);
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('delete', account)")
     */
    public function deleteAction(Account $account, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getAccountDeleteForm($account);
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($account);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form);
        }
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('view', account)")
     */
    public function getAction(Account $account)
    {
        $view = $this->view($account, 200);
        return $this->handleView($view);
    }

    /**
     * @Security("has_role('ROLE_USER')")
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
     * @Security("is_granted('edit', account)")
     */
    public function editAction(Account $account)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm($account, 'put_account', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @Security("is_granted('edit', account)")
     */
    public function putAction(Account $account, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm($account, 'put_account', 'PUT');
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
     * @Security("is_granted('view', account)")
     * @Get("/account/{id}/balance")
     */
    public function getBalanceAction(Account $account)
    {
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
