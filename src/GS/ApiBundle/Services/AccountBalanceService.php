<?php

namespace GS\ApiBundle\Services;

use Doctrine\ORM\EntityManager;

use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\Registration;

class AccountBalanceService
{
    private $entityManager;
    
    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getBalance(Account $account)
    {
        $registrations = $this->entityManager
            ->getRepository('GSApiBundle:Registration')
            ->getValidatedRegistrationsForAccount($account);

        $details = array();
        $totalDue = 0.0;
        for ($i = 0; $i < count($registrations); $i++) {
            $registration = $registrations[$i];
            $line = $this->getPriceToPay($i, $account, $registration);
            $details[] = $line;
            $totalDue += $line['due'];
        }

        $balance = array(
            'details' => $details,
            'totalDue' => $totalDue,
        );
        return $balance;
    }

    private function getPriceToPay($i, Account $account, Registration $registration)
    {
        $topic = $registration->getTopic();
        $category = $topic->getCategory();
        $discounts = $category->getDiscounts();
        $discount = $this->applyDiscounts($i, $account, $discounts);
        $price = $category->getPrice();
        $line = array(
            'registrationId' => $registration->getId(),
            'name' => $topic->getTitle(),
            'description' => $topic->getDescription(),
            'price' => $price,
        );
        $due = $price;
        if (null !== $discount) {
            $line['discount'] = array(
                'type' => $discount->getType(),
                'value' => $discount->getValue(),
            );
            if($discount->getType() == 'percent') {
                $due *= 1 - $discount->getValue() / 100;
            } else {
                $due -= $discount->getValue();
            }
        }
        $line['due'] = $due;
        return $line;
    }
    
    private function applyDiscounts($i, Account $account, $discounts)
    {
        foreach($discounts as $discount) {
            if($i >= 4 && $discount->getCondition() == '5th') {
                return $discount;
            } elseif($i >= 3 && $discount->getCondition() == '4th') {
                return $discount;
            } elseif($i >= 2 && $discount->getCondition() == '3rd') {
                return $discount;
            } elseif($i >= 1 && $discount->getCondition() == '2nd') {
                return $discount;
            } elseif($account->isStudent() && $discount->getCondition() == 'student') {
                return $discount;
            } elseif($account->isMember() && $discount->getCondition() == 'member') {
                return $discount;
            }
        }
        return null;
    }

}