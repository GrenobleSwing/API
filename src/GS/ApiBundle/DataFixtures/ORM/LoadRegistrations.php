<?php

namespace GS\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use GS\StructureBundle\Entity\Invoice;
use GS\StructureBundle\Entity\Payment;
use GS\StructureBundle\Entity\PaymentItem;
use GS\StructureBundle\Entity\Registration;

class LoadRegistrations extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
//        for ($i = 0; $i < 10; $i++) {
//            $account = $this->getReference('account'.$i);
//
//            if (0 == $i % 2) {
//                $role = 'leader';
//            } else {
//                $role = 'follower';
//            }
//
//            if (0 == $i % 3) {
//                $state = 'WAITING';
//            } elseif (1 == $i % 3) {
//                $state = 'VALIDATED';
//            } else {
//                $state = 'PAID';
//            }
//            
//            $registration1 = new Registration();
//            $registration1->setRole($role);
//            $registration1->setState($state);
//            $registration1->setAccount($account);
//            $registration1->setTopic($this->getReference('topic1'));
//            
//            $registration2 = new Registration();
//            $registration2->setRole($role);
//            $registration2->setState($state);
//            $registration2->setAccount($account);
//            $registration2->setTopic($this->getReference('topic2'));
//            
//            $registration3 = new Registration();
//            $registration3->setRole($role);
//            $registration3->setState($state);
//            $registration3->setAccount($account);
//            $registration3->setTopic($this->getReference('topic3'));
//            
//            $registration4 = new Registration();
//            $registration4->setRole($role);
//            $registration4->setState($state);
//            $registration4->setAccount($account);
//            $registration4->setTopic($this->getReference('topic4'));
//            
//            $registration5 = new Registration();
//            $registration5->setRole($role);
//            $registration5->setAccount($account);
//            $registration5->setTopic($this->getReference('topic5'));
//            $registration5->setState('PAID');
//            $registration5->setAmountPaid($registration5->getTopic()->getCategory()->getPrice());
//
//            $paymentItem1 = new PaymentItem();
//            $paymentItem1->setRegistration($registration5);
//            $paymentItem1->setAmount($registration5->getAmountPaid());
//
//            $payment1 = new Payment();
//            $payment1->setDate(new \DateTime());
//            $payment1->setState('PAID');
//            $payment1->setType('CHECK');
//            $payment1->addItem($paymentItem1);
//
//            $invoice1 = new Invoice($payment1);
//            $invoice1->setNumber('2017' . sprintf('%05d', $i));
//            $invoice1->setDate(new \DateTime());
//
//            $manager->persist($registration2);
//            $manager->persist($registration3);
//            $manager->persist($registration4);
//            $manager->persist($registration5);
//            $manager->persist($payment1);
//            $manager->persist($invoice1);
//        }
//        
//        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 3;
    }
}
