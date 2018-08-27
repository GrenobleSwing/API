<?php

namespace GS\ApiBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Role\Role;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use GS\StructureBundle\Entity\Account;
use GS\StructureBundle\Entity\Address;
use GS\StructureBundle\Entity\User;
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
     *   output="GS\StructureBundle\Entity\User",
     *   statusCodes={
     *     200="Returns the User",
     *   }
     * )
     * @Get("/identity")
     * @Security("has_role('ROLE_USER')")
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
     * @Get("/disconnect")
     * @Security("has_role('ROLE_USER')")
     */
    public function DisconnectAction()
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
     *   output="GS\StructureBundle\Entity\Account",
     *   statusCodes={
     *     200="Returns the Account",
     *   }
     * )
     * @Security("has_role('ROLE_USER')")
     */
    public function getAccountAction(User $user)
    {
        $account = $this->getDoctrine()->getManager()
            ->getRepository('GSStructureBundle:Account')
            ->findOneByUser($user)
            ;
        $view = $this->view($account, 200);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="User",
     *   description="Returns a form to create a new User",
     *   output="GS\StructureBundle\Form\Type\UserRegistrationType",
     *   statusCodes={
     *     200="You have permission to create an User, the form is returned",
     *   }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function newAction()
    {
        $form = $this->get('gsapi.form_generator')->getUserForm(null, 'gs_api_post_user');
        $view = $this->get('gsapi.form_generator')->getFormView($form);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="User",
     *   description="Create a new User",
     *   input="GS\StructureBundle\Form\Type\UserType",
     *   statusCodes={
     *     201="The User has been created",
     *   }
     * )
     * @Security("is_granted('IS_AUTHENTICATED_ANONYMOUSLY')")
     */
    public function postAction(Request $request)
    {
        $user = new User();
        $form = $this->get('gsapi.form_generator')->getUserForm($user, 'gs_api_post_user');

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setEnabled(true);
            $userManager = $this->get('fos_user.user_manager');
            $userManager->updateUser($user);

            $address = new Address();
            $account = new Account();
            $account->setUser($user);
            $account->setEmail($user->getEmail());
            $account->setAddress($address);

            $em = $this->getDoctrine()->getManager();
            $em->persist($account);
            $em->flush();

            $view = $this->view(array('id' => $user->getId()), 201);
        } else {
            $view = $this->get('gsapi.form_generator')->getFormView($form, 412);
        }

        return $this->handleView($view);
    }
}
