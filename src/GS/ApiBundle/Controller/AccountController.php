<?php

namespace GS\ApiBundle\Controller;

use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Form\Type\AccountType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class AccountController extends Controller
{

    /**
     * @Route("/my_account", name="my_account")
     * @Security("has_role('ROLE_USER')")
     */
    public function myAction(Request $request)
    {
        $account = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->findOneByUser($this->getUser())
            ;

        if ( null === $account ) {
            $request->getSession()->getFlashBag()->add('danger', "Le profil demandé n'existe pas.");
            return $this->redirectToRoute('homepage');
        }

        $listRegistrations = $this->getRegistrations($account, $request);

        return $this->render('GSApiBundle:Account:view.html.twig', array(
            'account' => $account,
            'listRegistrations' => $listRegistrations,
        ));
    }

    /**
     * @Route("/account/{id}", name="view_account", requirements={"id": "\d+"})
     * @Security("is_granted('view', account)")
     */
    public function getAction(Account $account, Request $request)
    {
        $listRegistrations = $this->getRegistrations($account, $request);
        return $this->render('GSApiBundle:Account:view.html.twig', array(
            'account' => $account,
            'listRegistrations' => $listRegistrations,
        ));
    }

    /**
     * @Route("/account", name="index_account")
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function cgetAction()
    {
        $listAccounts = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->findAll()
            ;

        return $this->render('GSApiBundle:Account:index.html.twig', array(
                    'listAccounts' => $listAccounts
        ));
    }

    /**
     * @Route("/account/{id}/edit", name="edit_account", requirements={"id": "\d+"})
     * @Security("is_granted('edit', account)")
     */
    public function putAction(Account $account, Request $request)
    {
        $form = $this->createForm(AccountType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Profil bien modifié.');

            return $this->redirectToRoute('view_account', array('id' => $account->getId()));
        }

        return $this->render('GSApiBundle:Account:edit.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/account/{id}/balance", name="balance_account", requirements={"id": "\d+"})
     * @Security("is_granted('view', account)")
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
        return new Response(json_encode($balance));
    }

    private function getRegistrations(Account $account, Request $request)
    {
        if ( $request->query->has('yearId') ) {
            $year = $this->getDoctrine()->getManager()
                    ->getRepository('GSApiBundle:Year')
                    ->find($request->query->get('yearId'));
            $registrations = $this->getDoctrine()->getManager()
                    ->getRepository('GSApiBundle:Registration')
                    ->getRegistrationsForAccountAndYear($account, $year);
        } else {
            $registrations = $this->getDoctrine()->getManager()
                    ->getRepository('GSApiBundle:Registration')
                    ->findBy(array('account' => $account));
        }

        return $registrations;
    }

}
