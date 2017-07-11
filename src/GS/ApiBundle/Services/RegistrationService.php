<?php

namespace GS\ApiBundle\Services;

use Doctrine\ORM\EntityManager;

use GS\ApiBundle\Entity\User;
use GS\ApiBundle\Entity\Registration;

class RegistrationService
{
    private $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function checkRequirements(Registration $registration, User $user)
    {
        $requirements = array();

        $topic = $registration->getTopic();
        $requiredTopics = $topic->getRequiredTopics();
        if (null === $requiredTopics ||
                count($requiredTopics) == 0) {
            return $requirements;
        }

        $account = $this->entityManager
            ->getRepository('GSApiBundle:Account')
            ->findOneByUser($user);

        foreach ($requiredTopics as $requiredTopic) {
            $results = $this->entityManager
                ->getRepository('GSApiBundle:Registration')
                ->getRegistrationsForAccountAndTopic($account, $requiredTopic);
            if (null === $results) {
                $requirements[] = $requiredTopic->getId();
            }
        }

        return $requirements;
    }

}