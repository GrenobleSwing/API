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
        $admin_user = new User();
        $admin_user->setEmail('admin@test.com');
        $admin_password = $this->container->get('security.password_encoder')
            ->encodePassword($admin_user, 'test');
        $admin_user->setPassword($admin_password);
        $admin_user->addRole('ROLE_ADMIN');

        $admin_phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
        $admin_account = new Account();
        $admin_account->setFirstName('Admin');
        $admin_account->setLastName('Admin');
        $admin_account->setAddress(new Address());
        $admin_account->setBirthDate(new \DateTime('1986-04-26'));
        $admin_account->setUser($admin_user);
        $admin_account->setEmail($admin_user->getEmail());
        $admin_account->setPhoneNumber($admin_phoneNumber);

        $this->addReference('admin_account', $admin_account);

        $manager->persist($admin_account);
        
        $organizer_user = new User();
        $organizer_user->setEmail('organizer@test.com');
        $organizer_password = $this->container->get('security.password_encoder')
            ->encodePassword($organizer_user, 'test');
        $organizer_user->setPassword($organizer_password);
        $organizer_user->addRole('ROLE_ADMIN');

        $organizer_phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
        $organizer_account = new Account();
        $organizer_account->setFirstName('Admin');
        $organizer_account->setLastName('Admin');
        $organizer_account->setAddress(new Address());
        $organizer_account->setBirthDate(new \DateTime('1986-04-26'));
        $organizer_account->setUser($organizer_user);
        $organizer_account->setEmail($organizer_user->getEmail());
        $organizer_account->setPhoneNumber($organizer_phoneNumber);

        $this->addReference('organizer_account', $organizer_account);

        $manager->persist($organizer_account);
        
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
