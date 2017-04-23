<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\ApiBundle\Entity\Account;

/**
 * @RouteResource("Account", pluralize=false)
 */
class AccountController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Returns a form to create a new Account",
     *   output="GS\ApiBundle\Form\Type\AccountType",
     *   statusCodes={
     *     200="You have permission to create a Account, the form is returned",
     *   }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm(null, 'post_account');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Create a new Account",
     *   input="GS\ApiBundle\Form\Type\AccountType",
     *   statusCodes={
     *     201="The Account has been created",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm(null, 'post_account');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            $view = $this->view(array('id' => $account->getId()), 201);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Returns a form to confirm deletion of a given Account",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Account, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', account)")
     */
    public function removeAction(Account $account)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($account, 'account');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Delete a given Account",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   input="GS\ApiBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Account has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', account)")
     */
    public function deleteAction(Account $account, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($account, 'account');
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
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
     * @ApiDoc(
     *   section="Account",
     *   description="Returns an existing Account",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   output="GS\ApiBundle\Entity\Account",
     *   statusCodes={
     *     200="Returns the Account",
     *   }
     * )
     * @Security("is_granted('view', account)")
     */
    public function getAction(Account $account)
    {
        $view = $this->view($account, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Returns a collection of Accounts",
     *   output="array<GS\ApiBundle\Entity\Account>",
     *   statusCodes={
     *     200="Returns all the Accounts",
     *   }
     * )
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
     * @ApiDoc(
     *   section="Account",
     *   description="Returns a form to edit an existing Account",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   output="GS\ApiBundle\Form\Type\AccountType",
     *   statusCodes={
     *     200="You have permission to create a Account, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', account)")
     */
    public function editAction(Account $account)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm($account, 'put_account', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Update an existing Account",
     *   input="GS\ApiBundle\Form\Type\AccountType",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Account has been updated",
     *   }
     * )
     * @Security("is_granted('edit', account)")
     */
    public function putAction(Account $account, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm($account, 'put_account', 'PUT');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $view = $this->view(null, 204);
            
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form);
        }
        return $this->handleView($view);
    }
    
    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Returns the balance of an existing Account",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   statusCodes={
     *     200="Return the Account's balance",
     *   }
     * )
     * @Security("is_granted('view', account)")
     * @Get("/account/{id}/balance")
     */
    public function getBalanceAction(Account $account, Request $request)
    {
        $activityId = $request->query->get('activityId');
        if (null !== $activityId) {
            $em = $this->getDoctrine()->getManager();
            $activity = $em->getRepository('GSApiBundle:Activity')->find($activityId);
        } else {
            $activity = null;
        }
        $balance = $this->get('gsapi.account_balance')->getBalance($account, $activity);
        $view = $this->view($balance, 200);
        return $this->handleView($view);
    }

}
