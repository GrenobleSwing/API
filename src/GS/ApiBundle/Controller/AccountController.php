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
        $balance = $this->get('gsapi.account_balance')->getBalance($account);
        $view = $this->view($balance, 200);
        return $this->handleView($view);
    }

}
