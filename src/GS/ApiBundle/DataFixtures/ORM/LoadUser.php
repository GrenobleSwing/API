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
//use GS\ApiBundle\Entity\Role;

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
        $userManager = $this->container->get('fos_user.user_manager');

        $admin_user = $userManager->createUser();
        $admin_user->setEmail('admin@test.com');
        $admin_user->setPlainPassword('test');
        $admin_user->setEnabled(true);
        $admin_user->addRole('ROLE_ADMIN');

        $admin_phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
        $admin_account = new Account();
        $admin_account->setFirstName('Admin');
        $admin_account->setLastName('Test');
        $admin_account->setAddress(new Address());
        $admin_account->setBirthDate(new \DateTime('1986-04-26'));
        $admin_account->setUser($admin_user);
        $admin_account->setEmail($admin_user->getEmail());
        $admin_account->setPhoneNumber($admin_phoneNumber);

        $this->addReference('admin_user', $admin_user);

        $manager->persist($admin_account);

        $organizer_user = $userManager->createUser();
        $organizer_user->setEmail('organizer@test.com');
        $organizer_user->setPlainPassword('test');
        $organizer_user->setEnabled(true);
        $organizer_user->addRole('ROLE_ORGANIZER');

        $organizer_phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
        $organizer_account = new Account();
        $organizer_account->setFirstName('Organizer');
        $organizer_account->setLastName('Test');
        $organizer_account->setAddress(new Address());
        $organizer_account->setBirthDate(new \DateTime('1986-04-26'));
        $organizer_account->setUser($organizer_user);
        $organizer_account->setEmail($organizer_user->getEmail());
        $organizer_account->setPhoneNumber($organizer_phoneNumber);

        $this->addReference('organizer_user', $organizer_user);

        $manager->persist($organizer_account);

//        $user1 = new User();
//        $user1->setEmail('john.doe@test.com');
//        $user1_password = $this->container->get('security.password_encoder')
//            ->encodePassword($user1, 'test');
//        $user1->setPassword($user1_password);
//
//        $user1_phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
//        $user1_account = new Account();
//        $user1_account->setFirstName('John');
//        $user1_account->setLastName('Doe');
//        $user1_account->setAddress(new Address());
//        $user1_account->setBirthDate(new \DateTime('1986-04-26'));
//        $user1_account->setUser($user1);
//        $user1_account->setEmail($user1->getEmail());
//        $user1_account->setPhoneNumber($user1_phoneNumber);
//
//        $user2 = new User();
//        $user2->setEmail('jane.doe@test.com');
//        $user2_password = $this->container->get('security.password_encoder')
//            ->encodePassword($user2, 'test');
//        $user2->setPassword($user2_password);
//
//        $user2_phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
//        $user2_account = new Account();
//        $user2_account->setFirstName('Jane');
//        $user2_account->setLastName('Doe');
//        $user2_account->setAddress(new Address());
//        $user2_account->setBirthDate(new \DateTime('1986-04-26'));
//        $user2_account->setUser($user2);
//        $user2_account->setEmail($user2->getEmail());
//        $user2_account->setPhoneNumber($user2_phoneNumber);
//
//        $manager->persist($user1_account);
//        $manager->persist($user2_account);
//
//        for ($i = 0; $i < 10; $i++) {
//            $user = new User();
//            $user->setEmail('bibi'.$i.'@test.com');
//            $password = $this->container->get('security.password_encoder')
//                ->encodePassword($user, 'test');
//            $user->setPassword($password);
//
//            $phoneNumber = $this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR');
//            $account = new Account();
//            $account->setFirstName('Toto'.$i);
//            $account->setLastName('Titi'.$i);
//            $account->setAddress(new Address());
//            $account->setBirthDate(new \DateTime('1986-04-26'));
//            $account->setUser($user);
//            $account->setEmail($user->getEmail());
//            $account->setPhoneNumber($phoneNumber);
//
//            $this->addReference('account'.$i, $account);
//
//            $manager->persist($account);
//        }

        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 1;
    }
}
