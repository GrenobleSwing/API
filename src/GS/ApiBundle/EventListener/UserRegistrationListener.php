<?php

namespace GS\ApiBundle\EventListener;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\Address;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class UserRegistrationListener implements EventSubscriberInterface
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_SUCCESS => 'onRegistrationSuccess',
        );
    }

    public function onRegistrationSuccess(FormEvent $event)
    {
        $user = $event->getForm()->getData();
        $address = new Address();
        $account = new Account();
        $account->setUser($user);
        $account->setEmail($user->getEmail());
        $account->setAddress($address);
        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }
}