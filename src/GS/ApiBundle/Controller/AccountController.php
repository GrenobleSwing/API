<?php

namespace GS\ApiBundle\Controller;

use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use GS\StructureBundle\Entity\Account;
use GS\ETransactionBundle\Entity\Payment;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * @RouteResource("Account", pluralize=false)
 */
class AccountController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Returns a form to create a new Account",
     *   output="GS\StructureBundle\Form\Type\AccountType",
     *   statusCodes={
     *     200="You have permission to create a Account, the form is returned",
     *   }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm(null, 'gs_api_post_account');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Create a new Account",
     *   input="GS\StructureBundle\Form\Type\AccountType",
     *   statusCodes={
     *     201="The Account has been created",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm(null, 'gs_api_post_account');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            $view = $this->view(array('id' => $account->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
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
     *   output="GS\StructureBundle\Form\Type\DeleteType",
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
     *   input="GS\StructureBundle\Form\Type\DeleteType",
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
            $view = $this->getFormView($form, 412);
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
     *   output="GS\StructureBundle\Entity\Account",
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
     *   output="array<GS\StructureBundle\Entity\Account>",
     *   statusCodes={
     *     200="Returns all the Accounts",
     *   }
     * )
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function cgetAction()
    {
        $listAccounts = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Account')
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
     *   output="GS\StructureBundle\Form\Type\AccountType",
     *   statusCodes={
     *     200="You have permission to create a Account, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', account)")
     */
    public function editAction(Account $account)
    {
        $form = $this->get('gsapi.form_generator')->getAccountForm($account, 'gs_api_put_account', 'PUT');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Update an existing Account",
     *   input="GS\StructureBundle\Form\Type\AccountType",
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
        $form = $this->get('gsapi.form_generator')->getAccountForm($account, 'gs_api_put_account', 'PUT');
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
     *   section="Account",
     *   description="Update the picture of an existing Account",
     *   input="GS\StructureBundle\Form\Type\AccountPictureType",
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
     * @Put("/account/{id}/picture")
     */
    public function putPictureAction(Account $account, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getAccountPictureForm($account, 'gs_api_put_account_picture', 'PUT');
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
     *   section="Account",
     *   description="Return the path of the picture of an existing Account",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   statusCodes={
     *     200="The Account's picture path",
     *   }
     * )
     * @Security("is_granted('view', account)")
     * @Get("/account/{id}/picture")
     */
    public function getPictureAction(Account $account)
    {
        $helper = $this->container->get('vich_uploader.templating.helper.uploader_helper');
        $path = $helper->asset($account, 'imageFile');
        $view = $this->view(array('path' => $path), 200);

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
            $activity = $em->getRepository('GSStructureBundle:Activity')->find($activityId);
        } else {
            $activity = null;
        }
        $balance = $this->get('gstoolbox.account_balance')->getBalance($account, $activity);

        $payment = $balance['payment'];
        $buttons = "";
        if ( null !== $payment) {
            $etranEnv = $payment->getItems()[0]
                    ->getRegistration()
                    ->getTopic()
                    ->getActivity()
                    ->getYear()
                    ->getSociety()
                    ->getPaymentEnvironment();

            if (null !== $etranEnv) {
                $transaction = new Payment();
                $transaction->setCmd($payment->getRef());
                $transaction->setEnvironment($etranEnv);
                $transaction->setPorteur($account->getEmail());
                $transaction->setTotal((int)($payment->getAmount() * 100));
                $transaction->setUrlAnnule($this->getParameter('return_url_cancelled'));
                $transaction->setUrlEffectue($this->getParameter('return_url_success'));
                $transaction->setUrlRefuse($this->getParameter('return_url_rejected'));
                $transaction->setIpnUrl($this->generateUrl('gse_transaction_ipn', array(), UrlGeneratorInterface::ABSOLUTE_URL));

                $buttons = $this->get('twig')->render('GSApiBundle:Payment:button.html.twig', array(
                        'payment' => $transaction,
                ));
            }
        }
        $balance['buttons'] = $buttons;
        unset($balance['payment']);

        $view = $this->view($balance, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Account",
     *   description="Returns all the registrations of a specified Account",
     *   requirements={
     *     {
     *       "name"="account",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Account id"
     *     }
     *   },
     *   output="array<GS\StructureBundle\Entity\Registration>",
     *   statusCodes={
     *     200="Returns the Registrations of the specified Account",
     *   }
     * )
     * @RequestParam(
     *   name="yearId",
     *   requirements="",
     *   default=null,
     *   description="",
     *   nullable=true
     * )
     * @Security("is_granted('view', account)")
     */
    public function getRegistrationsAction(Account $account, Request $request)
    {
        if ( $request->query->has('yearId') ) {
            $year = $this->getDoctrine()->getManager()
                    ->getRepository('GSStructureBundle:Year')
                    ->find($request->query->get('yearId'));
            $registrations = $this->getDoctrine()->getManager()
                    ->getRepository('GSStructureBundle:Registration')
                    ->getRegistrationsForAccountAndYear($account, $year);
        }
        else {
            $registrations = $this->getDoctrine()->getManager()
                    ->getRepository('GSStructureBundle:Registration')
                    ->findBy(array('account' => $account));
        }

        $context = new Context();
        $context->setGroups(array(
            'Default',
            'topic' => array(
                'registration_group'
            ),
        ));

        $view = $this->view($registrations, 200);
        $view->setContext($context);
        return $this->handleView($view);
    }

}
