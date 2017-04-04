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
        $totalBalance = 0.0;
        $i = 0;
        $currentActivity = null;
        $currentCategory = null;
        
        // Registrations are sorted by Category and Price.
        // All Discounts are linked to Category and they apply from the most
        // expensive Category to the less expensive one.
        foreach ($registrations as $registration) {
            $activity = $registration->getTopic()->getActivity();
            $category = $registration->getTopic()->getCategory();
            
            // For a better display, we group registrations by activity.
            if ($currentActivity !== $activity) {
                $currentActivity = $activity;
                
                // We append the name of the year for better display
                $displayName = $currentActivity->getTitle() . ' - ' .
                        $activity->getYear()->getTitle();
                $details[$displayName] = array();
                $i = 0;
            }
            
            // When we change Category, we reset the index of the Registration
            // since some discount are based on the number of Topics having the
            // same Category.
            if ($currentCategory !== $category) {
                $currentCategory = $category;
                $details[$displayName][$currentCategory->getName()] = array();
                $i = 0;
            }
            
            $line = $this->getPriceToPay($i, $account, $registration);
            $details[$displayName][$currentCategory->getName()][] = $line;
            $totalBalance += $line['balance'];
            $i++;
        }

        $balance = array(
            'details' => $details,
            'totalBalance' => $totalBalance,
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
        $alreadyPaid = $registration->getAmountPaid();
        
        $line = array(
            'registrationId' => $registration->getId(),
            'name' => $topic->getTitle(),
            'description' => $topic->getDescription(),
            'price' => $price,
            'alreadyPaid' => $alreadyPaid,
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
        
        $line['balance'] = $due - $alreadyPaid;
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