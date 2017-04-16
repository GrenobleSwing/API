<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;

class UserController extends FOSRestController
{
    /**
     * @Get("/identity")
     */
    public function IdentityAction()
    {
        $user = $this->getUser();
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }
}
