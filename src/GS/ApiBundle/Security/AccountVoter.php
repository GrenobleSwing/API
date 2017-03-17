<?php

namespace GS\ApiBundle\Security;

use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManagerInterface;

use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\User;

class AccountVoter extends Voter
{
    // these strings are just invented: you can use anything
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
        if (!in_array($attribute, array(self::VIEW, self::EDIT, self::DELETE))) {
            return false;
        }

        // only vote on Account objects inside this voter
        if (!$subject instanceof Account) {
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

        // you know $subject is a Account object, thanks to supports
        $account = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($account, $user);
            case self::EDIT:
                return $this->canEdit($account, $user);
            case self::DELETE:
                return $this->canDelete($account, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Account $account, User $user)
    {
        // if they can edit, they can view
        if ($this->canEdit($account, $user)) {
            return true;
        }
        return false;
    }

    private function canEdit(Account $account, User $user)
    {
        return $user === $account->getUser();
    }

    private function canDelete(Account $account, User $user)
    {
        return $user === $account->getUser();
    }

}