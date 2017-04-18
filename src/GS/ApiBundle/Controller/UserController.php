<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\Security\Core\Role\Role;
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
        $roles = array();
        foreach ($user->getRoles() as $role) {
            $roles[] = new Role($role);
        }
        $all_roles = array();
        foreach ($this->get('security.role_hierarchy')->getReachableRoles($roles) as $role) {
            $all_roles[] = $role->getRole();
        }
        $result = array(
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'active' => $user->isActive(),
            'roles' => array_values(array_unique($all_roles)),
        );
        $view = $this->view($result, 200);
        return $this->handleView($view);
    }
}
