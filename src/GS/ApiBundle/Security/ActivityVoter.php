<?php

namespace GS\ApiBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Entity\User;

class ActivityVoter extends Voter
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

        // only vote on Activity objects inside this voter
        if (!$subject instanceof Activity) {
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

        // you know $subject is a Activity object, thanks to supports
        $activity = $subject;

        switch ($attribute) {
            case self::CREATE:
                return $this->canCreate($activity, $user, $token);
            case self::VIEW:
                return $this->canView($activity, $user, $token);
            case self::EDIT:
                return $this->canEdit($activity, $user, $token);
            case self::DELETE:
                return $this->canDelete($activity, $user, $token);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canCreate(Activity $activity, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ORGANIZER'))) {
            return true;
        } elseif (null != $activity->getYear()) {
            foreach ($activity->getYear()->getOwners() as $owner) {
                if ($user === $owner) {
                    return true;
                }
            }
        }
        return false;
    }

    private function canView(Activity $activity, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_USER'))) {
            return true;
        }
        return false;
    }

    private function canEdit(Activity $activity, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
        foreach ($activity->getOwners() as $owner) {
            if ($user === $owner) {
                return true;
            }
        }
        return false;
    }

    private function canDelete(Activity $activity, User $user, TokenInterface $token)
    {
        if ($this->decisionManager->decide($token, array('ROLE_ADMIN'))) {
            return true;
        }
        if ('DRAFT' == $activity->getState()) {
            foreach ($activity->getOwners() as $owner) {
                if ($user === $owner) {
                    return true;
                }
            }
        }
        return false;
    }

}