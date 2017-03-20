<?php

namespace GS\ApiBundle\Repository;

use GS\ApiBundle\Entity\Account;

/**
 * RegistrationRepository
 */
class RegistrationRepository extends \Doctrine\ORM\EntityRepository
{

    public function getValidatedRegistrationsForAccount(Account $account)
    {
        $qb = $this->createQueryBuilder('reg');
        $qb
                ->where('reg.account = :acc')
                ->andWhere('reg.state = :state')
                ->leftJoin('reg.topic', 'top')
                ->addSelect('top')
                ->leftJoin('top.category', 'cat')
                ->addSelect('cat')
                ->orderBy('cat.price', 'DESC')
                ->setParameter('acc', $account)
                ->setParameter('state', 'validated');
        return $qb->getQuery()->getResult();
    }

}
