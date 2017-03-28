<?php

namespace GS\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use GS\ApiBundle\Entity\Registration;

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
        for ($i = 0; $i < 10; $i++) {
            $account = $this->getReference('account'.$i);

            if (0 == $i % 3) {
                $state = 'waiting';
            } elseif (1 == $i % 3) {
                $state = 'validated';
            } else {
                $state = 'paid';
            }
            
            $registration1 = new Registration();
            $registration1->setRole('leader');
            $registration1->setState($state);
            $registration1->setAccount($account);
            $registration1->setTopic($this->getReference('topic1'));
            
            $registration2 = new Registration();
            $registration2->setRole('leader');
            $registration2->setState($state);
            $registration2->setAccount($account);
            $registration2->setTopic($this->getReference('topic2'));
            
            $registration3 = new Registration();
            $registration3->setRole('leader');
            $registration3->setState($state);
            $registration3->setAccount($account);
            $registration3->setTopic($this->getReference('topic3'));
            
            $registration4 = new Registration();
            $registration4->setRole('leader');
            $registration4->setState($state);
            $registration4->setAccount($account);
            $registration4->setTopic($this->getReference('topic4'));
            
            $manager->persist($registration1);
            $manager->persist($registration2);
            $manager->persist($registration3);
            $manager->persist($registration4);
        }
        
        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 3;
    }
}