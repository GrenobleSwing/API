<?php

namespace GS\ApiBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

use GS\ApiBundle\Entity\Registration;
use GS\ApiBundle\Entity\User;

class RegistrationVoter extends Voter
{
    // these strings are just invented: you can use anything
    const CREATE = 'create';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $decisionManager;

    public function __construct(AccessDecisionManagerInterface $decisionManager)
    {
        $this->decisionManager = $decisionManager;
    }
    
    protected function supports($attribute, $subject)
    {
        // if the attribute isn't one we support, return false
        if (!in_array($attribute, array(self::CREATE, self::VIEW, self::EDIT, self::DELETE))) {
            return false;
        }

        // only vote on Registration objects inside this voter
        if (!$subject instanceof Registration) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();

        if (!$user instanceof User) {
            // the user must be logged in; if not, deny access
            return false;
        }

        // you know $subject is a Registration object, thanks to supports
        $registration = $subject;

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($registration, $user, $token);
            case self::VIEW:
                return $this->canView($registration, $user, $token);
            case self::EDIT:
                return $this->canEdit($registration, $user, $token);
            case self::DELETE:
                return $this->canDelete($registration, $user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreate(Registration $registration, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_USER'))) {
            return true;
        }
        return false;
    }
    
    private function canView(Registration $registration, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
        $editors = new ArrayCollection(
                array_merge($registration->getTopic()->getOwners()->toArray(),
                        $registration->getTopic()->getManagers()->toArray(),
                        $registration->getTopic()->getActivity()->getOwners()->toArray())
        );
        foreach ($editors as $editor) {
            if ($user === $editor) {
                return true;
            }
        }
        return false;
    }

    private function canEdit(Registration $registration, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
        if ($user === $registration->getAccount()->getUser() &&
                'SUBMITTED' == $registration->getState()) {
            return true;
        }
        $editors = new ArrayCollection(
                array_merge($registration->getTopic()->getOwners()->toArray(),
                        $registration->getTopic()->getManagers()->toArray(),
                        $registration->getTopic()->getActivity()->getOwners()->toArray())
        );
        foreach ($editors as $editor) {
            if ($user === $editor) {
                return true;
            }
        }
        return false;
    }

    private function canDelete(Registration $registration, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
        return false;
    }

}