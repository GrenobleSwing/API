<?php

namespace GS\ApiBundle\Controller;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations\Get;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

use GS\StructureBundle\Entity\Registration;

/**
 * @RouteResource("Registration", pluralize=false)
 */
class RegistrationController extends FOSRestController
{

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to VALIDATED",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been validated",
     *   }
     * )
     * @Security("is_granted('validate', registration)")
     * @Get("/registration/{id}/validate")
     */
    public function validateAction(Registration $registration)
    {
        if (!in_array($registration->getState(), array('SUBMITTED', 'WAITING'))) {
            throw new MethodNotAllowedHttpException('Impossible to validate Registration');
        }
        $registration->validate();
        $em = $this->getDoctrine()->getManager();

        $this->fulfillMembershipRegistration($registration, $em);

        # In case of a registration with a partner, validate also the partner
        if (null !== $registration->getPartnerRegistration()) {
            $registration->getPartnerRegistration()->validate();
            $this->fulfillMembershipRegistration($registration->getPartnerRegistration(), $em);
        }
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    # Check if the membership is mandatory for the Registration
    # and do the needed work in case it is.
    private function fulfillMembershipRegistration (Registration $registration, EntityManager $em) {
        $topic = $registration->getTopic();
        $account = $registration->getAccount();
        $activity = $topic->getActivity();
        $year = $activity->getYear();

        if ($activity->getMembersOnly() &&
                !$this->get('gsapi.user.membership')->isMember($account, $year) &&
                null !== $activity->getMembershipTopic()) {
            $membership = new Registration();
            $membership->setAccount($account);
            $membership->setTopic($activity->getMembershipTopic());
            $membership->validate();
            $em->persist($membership);
        }
        return $this;
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to WAITING",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been put in waiting list",
     *   }
     * )
     * @Security("is_granted('wait', registration)")
     * @Get("/registration/{id}/wait")
     */
    public function waitAction(Registration $registration)
    {
        if ('SUBMITTED' != $registration->getState()) {
            throw new MethodNotAllowedHttpException('Impossible to put Registration in waiting list');
        }
        $registration->wait();
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to CANCELLED",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been cancelled",
     *   }
     * )
     * @Security("is_granted('cancel', registration)")
     * @Get("/registration/{id}/cancel")
     */
    public function cancelAction(Registration $registration)
    {
        if (in_array($registration->getState(), array('CANCELLED', 'PARTIALLY_CANCELLED'))) {
            throw new MethodNotAllowedHttpException('Impossible to cancel Registration');
        }
        $registration->cancel();

        if (null !== $registration->getPartnerRegistration()) {
            $registration->getPartnerRegistration()->setPartnerRegistration(null);
            $registration->setPartnerRegistration(null);
        }

        $em = $this->getDoctrine()->getManager();

        // If the registration is not paid, there is no need to keep it.
        if ($registration->getState() != 'PAID') {
            $em->remove($registration);
        }

        $this->get('gsapi.registration.service')->onCancel($registration);

        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Changes a given Registration state to PAID",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The given Registration has been paid",
     *   }
     * )
     * @Security("is_granted('pay', registration)")
     * @Get("/registration/{id}/pay")
     */
    public function payAction(Registration $registration)
    {
        if ('VALIDATED' != $registration->getState()) {
            throw new MethodNotAllowedHttpException('Impossible to mark Registration as paid');
        }
        $registration->pay();
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Create a new Registration",
     *   input="GS\StructureBundle\Form\Type\RegistrationType",
     *   statusCodes={
     *     201="The Registration has been created",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function postAction(Request $request)
    {
        $registration = new Registration();
        $account = $this->getDoctrine()
                ->getRepository('GSApiBundle:Account')
                ->findOneByUser($this->getUser());
        $registration->setAccount($account);

        $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'gs_api_post_registration');
        $this->denyAccessUnlessGranted('create', $form->getData());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $registration = $form->getData();

            if ($registration->getWithPartner() &&
                    null === $registration->getPartnerRegistration()) {
                $partner = $this->findPartner($registration);

                if (null !== $partner &&
                        null === $partner->getPartnerRegistration()) {
                    $registration->setPartnerRegistration($partner);
                }
            }

            $topic = $registration->getTopic();
            $topic->addRegistration($registration);

            if ($topic->getAutoValidation()) {
                $registration->validate();
            }

            $this->get('gsapi.registration.service')->onSubmitted($registration);

            $em = $this->getDoctrine()->getManager();
            $em->persist($registration);
            $em->flush();

            $view = $this->view(array('id' => $registration->getId()), 201);

        } else {
            $view = $this->get('gsapi.form_generator')->getRegistrationFormView($registration, $form, 412);
        }
        return $this->handleView($view);
    }

    private function findPartner (Registration $registration)
    {
        $partnerAccount = $this->findPartnerAccount($registration);
        if ($partnerAccount === null) {
            return null;
        }
        if ($registration->getRole() == 'leader') {
            $partnerRole = 'follower';
        } else {
            $partnerRole = 'leader';
        }
        $partnerRegistrations = $this->getDoctrine()
                ->getRepository('GSApiBundle:Registration')
                ->findBy(array(
                    'account' => $partnerAccount,
                    'topic' => $registration->getTopic(),
                    'role' => $partnerRole,
                    ));
        if ($partnerRegistrations === null || count($partnerRegistrations) != 1) {
            return null;
        }
        return $partnerRegistrations[0];
    }

    private function findPartnerAccount (Registration $registration)
    {
        $partnerAccounts = null;
        if ($registration->getPartnerEmail() !== null) {
            $partnerAccounts = $this->getDoctrine()
                    ->getRepository('GSApiBundle:Account')
                    ->findByEmail($registration->getPartnerEmail());
        }
        elseif ($registration->getPartnerFirstName() !== null &&
                $registration->getPartnerLastName() !== null) {
            $partnerAccounts = $this->getDoctrine()
                    ->getRepository('GSApiBundle:Account')
                    ->findBy(array(
                        'firstName' => $registration->getPartnerFirstName(),
                        'lastName' => $registration->getPartnerLastName()));
        }

        $partnerAccount = null;
        if ($partnerAccounts !== null && count($partnerAccounts) == 1) {
            $partnerAccount = $partnerAccounts[0];
        }
        return $partnerAccount;
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns a form to confirm deletion of a given Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     200="You have permission to delete a Registration, the form is returned",
     *   }
     * )
     * @Security("is_granted('delete', registration)")
     */
    public function removeAction(Registration $registration)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($registration, 'registration');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Delete a given Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   input="GS\StructureBundle\Form\Type\DeleteType",
     *   statusCodes={
     *     204="The Registration has been deleted",
     *   }
     * )
     * @Security("is_granted('delete', registration)")
     */
    public function deleteAction(Registration $registration, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getDeleteForm($registration, 'registration');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->get('gsapi.registration.service')->cleanPayments($registration);

            $em = $this->getDoctrine()->getManager();
            $em->remove($registration);
            $em->flush();

            $view = $this->view(null, 204);
        } else {
            $view = $this->getFormView($form, 412);
        }
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns an existing Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   output="GS\StructureBundle\Entity\Registration",
     *   statusCodes={
     *     200="Returns the Registration",
     *   }
     * )
     * @Security("is_granted('view', registration)")
     */
    public function getAction(Registration $registration)
    {
        $context = new Context();
        $context->setGroups(array(
            'Default',
            'topic' => array(
                'registration_group'
            ),
        ));

        $view = $this->view($registration, 200);
        $view->setContext($context);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns a collection of Registrations",
     *   output="array<GS\StructureBundle\Entity\Registration>",
     *   statusCodes={
     *     200="Returns all the Registrations",
     *   }
     * )
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function cgetAction()
    {
        $listRegistrations = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Registration')
            ->findAll()
            ;

        $context = new Context();
        $context->setGroups(array(
            'Default',
            'topic' => array(
                'registration_group'
            ),
        ));

        $view = $this->view($listRegistrations, 200);
        $view->setContext($context);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Returns a form to edit an existing Registration",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   output="GS\StructureBundle\Form\Type\RegistrationType",
     *   statusCodes={
     *     200="You have permission to create a Registration, the form is returned",
     *   }
     * )
     * @Security("is_granted('edit', registration)")
     */
    public function editAction(Registration $registration)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'gs_api_put_registration', 'PUT');
        $view = $this->get('gsapi.form_generator')->getRegistrationFormView($registration, $form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Registration",
     *   description="Update an existing Registration",
     *   input="GS\StructureBundle\Form\Type\RegistrationType",
     *   requirements={
     *     {
     *       "name"="registration",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="Registration id"
     *     }
     *   },
     *   statusCodes={
     *     204="The Registration has been updated",
     *   }
     * )
     * @Security("is_granted('edit', registration)")
     */
    public function putAction(Registration $registration, Request $request)
    {
        $form = $this->get('gsapi.form_generator')->getRegistrationForm($registration, 'gs_api_put_registration', 'PUT');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $view = $this->view(null, 204);

        } else {
            $view = $this->get('gsapi.form_generator')->getRegistrationFormView($registration, $form, 412);
        }
        return $this->handleView($view);
    }

}
