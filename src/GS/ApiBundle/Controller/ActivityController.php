<?php

namespace GS\ApiBundle\Controller;

use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Entity\Year;
use GS\ApiBundle\Form\Type\ActivityType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends Controller
{
    /**
     * @Route("/activity/{id}/open", name="open_activity", requirements={"id": "\d+"})
     * @Security("is_granted('edit', activity)")
     */
    public function openAction(Activity $activity, Request $request)
    {
        if ('DRAFT' != $activity->getState()) {
            $request->getSession()->getFlashBag()->add('danger', "Impossible to open activity");
            return $this->redirectToRoute('view_activity', array('id' => $activity->getId()));
        }

        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $activity->setState('OPEN');
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "L'activité a bien été ouverte.");

            return $this->redirectToRoute('view_activity', array('id' => $activity->getId()));
        }

        return $this->render('GSApiBundle:Activity:open.html.twig', array(
            'activity' => $activity,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/activity/{id}/close", name="close_activity", requirements={"id": "\d+"})
     * @Security("is_granted('edit', activity)")
     */
    public function closeAction(Activity $activity, Request $request)
    {
        if ('OPEN' != $activity->getState()) {
            $request->getSession()->getFlashBag()->add('danger', "Impossible to close activity");
            return $this->redirectToRoute('view_activity', array('id' => $activity->getId()));
        }

        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $activity->setState('CLOSE');
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "L'activité a bien été fermée.");

            return $this->redirectToRoute('view_activity', array('id' => $activity->getId()));
        }

        return $this->render('GSApiBundle:Activity:close.html.twig', array(
            'activity' => $activity,
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/activity/add/{id}", name="add_activity", requirements={"id": "\d+"})
     * @Security("has_role('ROLE_ORGANIZER')")
     */
    public function addAction(Year $year, Request $request)
    {
        $activity = new Activity();
        $activity->setYear($year);
        $form = $this->createForm(ActivityType::class, $activity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $activity = $form->getData();
            $activity->addOwner($this->getUser());

            $em = $this->getDoctrine()->getManager();
            $em->persist($activity);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Activité bien enregistrée.');

            return $this->redirectToRoute('view_activity', array('id' => $activity->getId()));
        }

        return $this->render('GSApiBundle:Activity:add.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * @Route("/activity/{id}/delete", name="delete_activity", requirements={"id": "\d+"})
     * @Security("is_granted('delete', activity)")
     */
    public function deleteAction(Activity $activity, Request $request)
    {
        $form = $this->createFormBuilder()->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $year = $activity->getYear();
            $year->removeActivity($activity);

            $em = $this->getDoctrine()->getManager();
            $em->remove($activity);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', "L'activité a bien été supprimée.");

            return $this->redirectToRoute('view_year', array('id' => $year->getId()));
        }

        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return $this->render('GSApiBundle:Activity:delete.html.twig', array(
                    'activity' => $activity,
                    'form' => $form->createView()
        ));
    }

    /**
     * @Route("/activity/{id}", name="view_activity", requirements={"id": "\d+"})
     * @Security("is_granted('view', activity)")
     */
    public function viewAction(Activity $activity)
    {
        $em = $this->getDoctrine()->getManager();
        $account = $em
            ->getRepository('GSApiBundle:Account')
            ->findOneByUser($this->getUser())
            ;

        $registrations = $em
            ->getRepository('GSApiBundle:Registration')
            ->getRegistrationsNotCancelledForAccountAndActivity($account, $activity);

        $topics = [];
        foreach ($registrations as $registration) {
            $topics[] = $registration->getTopic();
        }
        return $this->render('GSApiBundle:Activity:view.html.twig', array(
            'activity' => $activity,
            'user_topics' => $topics,
        ));
    }

    /**
     * @Route("/activity", name="index_activity")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction()
    {
        $listActivities = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Activity')
            ->findAll()
            ;

        return $this->render('GSApiBundle:Activity:index.html.twig', array(
                    'listActivities' => $listActivities
        ));
    }

    /**
     * @Route("/activity/{id}/edit", name="edit_activity", requirements={"id": "\d+"})
     * @Security("is_granted('edit', activity)")
     */
    public function editAction(Activity $activity, Request $request)
    {
        $form = $this->createForm(ActivityType::class, $activity);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Activité bien modifiée.');

            return $this->redirectToRoute('view_activity', array('id' => $activity->getId()));
        }

        return $this->render('GSApiBundle:Activity:edit.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

}
