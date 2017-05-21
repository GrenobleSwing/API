<?php

namespace GS\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Entity\Address;
use GS\ApiBundle\Entity\Category;
use GS\ApiBundle\Entity\Discount;
use GS\ApiBundle\Entity\Schedule;
use GS\ApiBundle\Entity\Society;
use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Entity\Venue;
use GS\ApiBundle\Entity\Year;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class LoadData extends AbstractFixture implements ContainerAwareInterface, OrderedFixtureInterface
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
        $address1 = new Address();
        $address1->setStreet('2 rue Mozart');
        $address1->setZipCode('38000');
        $address1->setCity('Grenoble');

        $venue = new Venue();
        $venue->setName('Les Planches');
        $venue->setAddress($address1);

        $manager->persist($venue);

        $address2 = new Address();
        $address2->setStreet('3 rue Henri Moissan');
        $address2->setZipCode('38100');
        $address2->setCity('Grenoble');

        $society = new Society();
        $society->setAddress($address2);
        $society->setPhoneNumber($this->container->get('libphonenumber.phone_number_util')->parse('0380581981', 'FR'));
        $society->setName('Grenoble Swing');
        $society->setEmail('info@grenobleswing.com');
        $society->setTaxInformation('SIRET : 22222222');
        $society->setVatInformation('TVA Intra : FR2222222');

        $manager->persist($society);

        $year1 = new Year();
        $year1->setTitle('Annee 2015-2016');
        $year1->setDescription('description pour annee 2015-2016');
        $year1->setStartDate(new \DateTime('2015-09-01'));
        $year1->setEndDate(new \DateTime('2016-08-31'));
        $year1->setState('open');
        
        $year2 = new Year();
        $year2->setTitle('Annee 2017-2018');
        $year2->setDescription('description pour annee 2017-2018');
        $year2->setStartDate(new \DateTime('2017-09-01'));
        $year2->setEndDate(new \DateTime('2018-08-31'));
        $year2->setState('open');

        $manager->persist($year1);
        $manager->persist($year2);
        
        $year = new Year();
        $year->setTitle('Annee 2016-2017');
        $year->setDescription('description pour annee 2016-2017');
        $year->setStartDate(new \DateTime('2016-09-01'));
        $year->setEndDate(new \DateTime('2017-08-31'));
        $year->setState('open');
        
        $activity1 = new Activity();
        $activity1->setTitle('Adhesion');
        $activity1->setDescription('Adhesion annuelle a Grenoble Swing');
        $activity1->setState('open');
        
        $category1 = new Category();
        $category1->setName('Adhesion');
        $category1->setPrice(10.0);
        
        $activity1->addCategory($category1);

        $schedule = new Schedule();
        $schedule->setFrequency('weekly');
        $schedule->setStartDate(new \DateTime('2016-09-14'));
        $schedule->setEndDate(new \DateTime('2017-06-24'));
        $schedule->setStartTime(new \DateTime('20:30'));
        $schedule->setEndTime(new \DateTime('21:30'));

        $topic1 = new Topic();
        $topic1->setTitle('Adhesion');
        $topic1->setDescription('Adhesion annuelle');
        $topic1->setType('adhesion');
        $topic1->setCategory($category1);
        $topic1->setState('open');
        $topic1->addOption('automatic_validation');
        
        $activity1->addTopic($topic1);
        
        $activity2 = new Activity();
        $activity2->setTitle('Cours et troupes');
        $activity2->setDescription('Les cours et les troupes');
        $activity2->setState('draft');
        
        $category2 = new Category();
        $category2->setName('Cours');
        $category2->setPrice(190.0);
        
        $category3 = new Category();
        $category3->setName('Troupe');
        $category3->setPrice(90.0);
        
        $discount1 = new Discount();
        $discount1->setName('Etudiant');
        $discount1->setType('percent');
        $discount1->setValue(20.0);
        $discount1->setCondition('student');
        
        $discount2 = new Discount();
        $discount2->setName('2e cours');
        $discount2->setType('percent');
        $discount2->setValue(30.0);
        $discount2->setCondition('2nd');
        
        $category2->addDiscount($discount1);
        $category2->addDiscount($discount2);
        
        $activity2->addCategory($category2);
        $activity2->addCategory($category3);
        $activity2->addDiscount($discount1);
        $activity2->addDiscount($discount2);
        
        $topic2 = new Topic();
        $topic2->setTitle('Lindy debutant');
        $topic2->setDescription('Cours de lindy');
        $topic2->setType('couple');
        $topic2->setState('open');
        $topic2->setCategory($category2);
        $topic2->addRequiredTopic($topic1);
        $topic2->addSchedule($schedule);
        
        $topic4 = new Topic();
        $topic4->setTitle('Lindy intermediaire');
        $topic4->setDescription('Cours de lindy');
        $topic4->setType('couple');
        $topic4->setState('open');
        $topic4->setCategory($category2);
        $topic4->addRequiredTopic($topic1);
        $topic4->addSchedule(clone $schedule);
        
        $topic5 = new Topic();
        $topic5->setTitle('Lindy avance');
        $topic5->setDescription('Cours de lindy');
        $topic5->setType('couple');
        $topic5->setState('open');
        $topic5->setCategory($category2);
        $topic5->addRequiredTopic($topic1);
        $topic5->addSchedule(clone $schedule);
        
        $topic3 = new Topic();
        $topic3->setTitle('Troupe avancee');
        $topic3->setDescription('Troupe avancee');
        $topic3->setType('couple');
        $topic3->setState('open');
        $topic3->setCategory($category3);
        $topic3->addRequiredTopic($topic1);
        $topic3->addSchedule(clone $schedule);
        
        $activity2->addTopic($topic2);
        $activity2->addTopic($topic3);
        $activity2->addTopic($topic4);
        $activity2->addTopic($topic5);
        
        $year->addActivity($activity1);
        $year->addActivity($activity2);
        
        $this->addReference('topic1', $topic1);
        $this->addReference('topic2', $topic2);
        $this->addReference('topic3', $topic3);
        $this->addReference('topic4', $topic4);
        $this->addReference('topic5', $topic5);
        
        $manager->persist($year);
        $manager->flush();
    }

    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 2;
    }
}
