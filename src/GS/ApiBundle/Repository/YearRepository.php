<?php

namespace GS\ApiBundle\Repository;

/**
 * YearRepository
 */
class YearRepository extends \Doctrine\ORM\EntityRepository
{

    public function findCurrentYear()
    {
        $now = new \DateTime();
        
        $qb = $this->createQueryBuilder('y');
        $qb
                ->where($qb->expr()->between(':date', 'y.startDate', 'y.endDate'))
                ->setParameter('date', $now, \Doctrine\DBAL\Types\Type::DATETIME)
                ;
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findPreviousYear()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval('P1Y'));
        
        $qb = $this->createQueryBuilder('y');
        $qb
                ->where($qb->expr()->between(':date', 'y.startDate', 'y.endDate'))
                ->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATETIME)
                ;
        return $qb->getQuery()->getOneOrNullResult();
    }

    public function findNextYear()
    {
        $date = new \DateTime();
        $date->add(new \DateInterval('P1Y'));
        
        $qb = $this->createQueryBuilder('y');
        $qb
                ->where($qb->expr()->between(':date', 'y.startDate', 'y.endDate'))
                ->setParameter('date', $date, \Doctrine\DBAL\Types\Type::DATETIME)
                ;
        return $qb->getQuery()->getOneOrNullResult();
    }

}
