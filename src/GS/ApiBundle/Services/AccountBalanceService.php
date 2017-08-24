<?php

namespace GS\ApiBundle\Services;

use Doctrine\ORM\EntityManager;

use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Entity\Category;
use GS\ApiBundle\Entity\Discount;
use GS\ApiBundle\Entity\Registration;

class AccountBalanceService
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function getBalanceForPaypal(Account $account, Activity $activity = null)
    {
        return $this->getDetails($account, $activity, true);
    }

    public function getBalance(Account $account, Activity $activity = null)
    {
        return $this->getDetails($account, $activity);
    }

    private function getDetails(Account $account, Activity $activity = null, $paypal = false)
    {
        if ($paypal) {
            return $this->getDetailsPaypal($account, $activity);
        }

        $registrations = $this->getRegistrations($account, $activity);

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

            // When we change Category, we reset the index of the Registration
            // since some discount are based on the number of Topics having the
            // same Category.
            if ($currentCategory !== $category && ! $paypal) {
                $currentCategory = $category;
                $i = 0;
            }

            // For a better display, we group registrations by activity.
            if ($currentActivity !== $activity) {
                $currentActivity = $activity;
                $i = 0;
            }

            // We append the name of the year for better display
            if ($currentActivity->isMembership()) {
                $displayName = $currentActivity->getTitle() . ' - ' .
                        $activity->getYear()->getTitle();
            } else {
                $displayName = $currentCategory->getName() . ' - ' .
                        $currentActivity->getTopic()->getTitle();
            }

            $discounts = $category->getDiscounts();
            $discount = $this->chooseDiscount($i, $account, $discounts);

            $line = $this->getPriceToPay($registration, $category, $discount);
            $line['title'] = $displayName;
            $details[] = $line;
            $totalBalance += $line['balance'];

            $i++;
        }

        $balance = array(
            'details' => $details,
            'totalBalance' => $totalBalance,
        );
        return $balance;
    }

    private function getDetailsPaypal(Account $account, Activity $activity = null)
    {
        $registrations = $this->getRegistrations($account, $activity);

        $details = array();
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
                $i = 0;
            }

            // When we change Category, we reset the index of the Registration
            // since some discount are based on the number of Topics having the
            // same Category.
            if ($currentCategory !== $category) {
                $currentCategory = $category;
                $i = 0;
            }

            $discounts = $category->getDiscounts();
            $discount = $this->chooseDiscount($i, $account, $discounts);

            $details[] = array($registration, $discount);
            $i++;
        }

        return $details;
    }

    private function getRegistrations(Account $account, Activity $activity = null)
    {
        if (null === $activity ) {
            $registrations = $this->entityManager
                ->getRepository('GSApiBundle:Registration')
                ->getValidatedRegistrationsForAccount($account);
        } else {
            $registrations = $this->entityManager
                ->getRepository('GSApiBundle:Registration')
                ->getRegistrationsForAccountAndActivity($account, $activity);
        }
        return $registrations;
    }

    private function getPriceToPay(Registration $registration, Category $category, Discount $discount = null)
    {
        $price = $category->getPrice();
        $alreadyPaid = $registration->getAmountPaid();

        $line = array(
            'price' => $price,
            'alreadyPaid' => $alreadyPaid,
        );
        $due = $price;

        if (null !== $discount) {
            if($discount->getType() == 'percent') {
                $line['discount'] = '-' . $discount->getValue() . '%';
                $due *= 1 - $discount->getValue() / 100;
            } else {
                $line['discount'] = '-' . $discount->getValue() . '&euro;';
                $due -= $discount->getValue();
            }
        } else {
            $line['discount'] = '';
        }

        $line['balance'] = $due - $alreadyPaid;
        return $line;
    }

    private function chooseDiscount($i, Account $account, $discounts)
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