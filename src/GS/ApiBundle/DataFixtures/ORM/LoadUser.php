<?php

namespace GS\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use GS\ApiBundle\Entity\User;
use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Entity\Address;

class LoadUser extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
            $user = new User();
            $user->setEmail('bibi'.$i.'@test.com');
            $password = $this->container->get('security.password_encoder')
                ->encodePassword($user, 'test');
            $user->setPassword($password);
            
            $phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
            $account = new Account();
            $account->setFirstName('Toto'.$i);
            $account->setLastName('Titi'.$i);
            $account->setAddress(new Address());
            $account->setBirthDate(new \DateTime('1986-04-26'));
            $account->setUser($user);
            $account->setEmail($user->getEmail());
            $account->setPhoneNumber($phoneNumber);
            
            $this->addReference('account'.$i, $account);
            
            $manager->persist($account);
        }

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}
