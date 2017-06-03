<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\Security\Core\Role\Role;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use GS\ApiBundle\Entity\User;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @RouteResource("User", pluralize=false)
 */
class UserController extends FOSRestController
{
    /**
     * @ApiDoc(
     *   section="User",
     *   description="Returns the current User",
     *   output="GS\ApiBundle\Entity\User",
     *   statusCodes={
     *     200="Returns the User",
     *   }
     * )
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

    /**
     * @ApiDoc(
     *   section="User",
     *   description="Logout the current User",
     *   statusCodes={
     *     200="The User has been logged out",
     *   }
     * )
     * @Get("/logout")
     */
    public function LogoutAction()
    {
        // Generate a new hash to invalidate the JWT
        // https://github.com/lexik/LexikJWTAuthenticationBundle/issues/58#issuecomment-89641970
        $user = $this->getUser();
        $user->setHash(uniqid('', true));
        $em = $this->getDoctrine()->getManager();
        $em->flush();

        $view = $this->view(null, 204);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="User",
     *   description="Returns the account of an existing User",
     *   requirements={
     *     {
     *       "name"="user",
     *       "dataType"="integer",
     *       "requirement"="\d+",
     *       "description"="User id"
     *     }
     *   },
     *   output="GS\ApiBundle\Entity\Account",
     *   statusCodes={
     *     200="Returns the Account",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function getAccountAction(User $user)
    {
        $account = $this->getDoctrine()->getManager()
            ->getRepository('GSApiBundle:Account')
            ->findOneByUser($user)
            ;
        $view = $this->view($account, 200);
        return $this->handleView($view);
    }


}
