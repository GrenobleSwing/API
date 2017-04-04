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
        // The first request is to get all the Category for which the Account
        // has at least one validated Registration.
        // With this special usage, parameters are share between the 2 queries
        // so it is useless to define them in the first query.
        $qbAcc = $this->createQueryBuilder('reg1');
        $qbAcc
                ->leftJoin('reg1.topic', 'top1')
                ->leftJoin('top1.category', 'cat1')
                ->select('cat1.id')
                ->where('reg1.account = :acc')
                ->andWhere('reg1.state = :statev');

        // The second request is to get all the validated or paid Registrations
        // for the Account using the Categories of first request to avoid all
        // the ones that only have paid Registrations.
        $qb = $this->createQueryBuilder('reg');
        $qb
                ->leftJoin('reg.topic', 'top')
                ->addSelect('top')
                ->leftJoin('top.category', 'cat')
                ->addSelect('cat')
                ->leftJoin('top.activity', 'act')
                ->addSelect('act')
                ->orderBy('act.title', 'ASC')
                ->orderBy('cat.name', 'ASC')
                ->addOrderBy('cat.price', 'DESC')
                ->where('reg.account = :acc')
                ->andWhere($qb->expr()->orX(
                    $qb->expr()->eq('reg.state', ':statev'),
                    $qb->expr()->eq('reg.state', ':statep')
                ))
                ->andWhere($qb->expr()->in('cat.id', $qbAcc->getDQL()))
                ->setParameter('acc', $account)
                ->setParameter('statev', 'VALIDATED')
                ->setParameter('statep', 'PAID');

        return $qb->getQuery()->getResult();
    }

}
