<?php

namespace GS\ApiBundle\EventListener;

use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserListener implements EventSubscriberInterface
{
    private $router;

    public function __construct(UrlGeneratorInterface $router)
    {
        $this->router = $router;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::CHANGE_PASSWORD_SUCCESS => 'onChangePasswordSuccess',
            FOSUserEvents::RESETTING_RESET_SUCCESS => 'onResettingPasswordSuccess',
        );
    }

    public function onChangePasswordSuccess(FormEvent $event)
    {
        $event->setResponse(new JsonResponse(null, 204));
    }

    public function onResettingPasswordSuccess(FormEvent $event)
    {
        $event->setResponse(new RedirectResponse('https://inscriptions.grenobleswing.com/'));
    }
}