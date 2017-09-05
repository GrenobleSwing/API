<?php

namespace GS\ApiBundle\Services;

use Doctrine\ORM\EntityManager;

use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\Year;

class MembershipService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function isAlmostMember(Account $account, Year $year)
    {
        $registrations = $this->entityManager
            ->getRepository('GSApiBundle:Registration')
            ->getMembershipRegistrationsForAccountAndYear($account, $year);

        foreach ($registrations as $registration) {
            if ($registration->getState() == 'PAID' ||
                    $registration->getState() == 'VALIDATED') {
                return true;
            }
        }
        return false;
    }

    public function isMember(Account $account, Year $year)
    {
        $registrations = $this->entityManager
            ->getRepository('GSApiBundle:Registration')
            ->getMembershipRegistrationsForAccountAndYear($account, $year);

        foreach ($registrations as $registration) {
            if ($registration->getState() == 'PAID') {
                return true;
            }
        }
        return false;
    }

    private function getMembers(Year $year)
    {
        $registrations = $this->entityManager
            ->getRepository('GSApiBundle:Registration')
            ->getMembershipRegistrationsForYear($year);

        $accounts = [];
        foreach ($registrations as $registration) {
            if ($registration->getState() == 'PAID') {
                $accounts[] = $registration->getAccount();
            }
        }

        return $accounts;
    }

}