<?php

namespace GS\ApiBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use GS\ApiBundle\Entity\Year;
use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Entity\Category;
use GS\ApiBundle\Entity\Discount;

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
        $topic2->setDay(1);
        $topic2->setType('couple');
        $topic2->setStartTime(new \DateTime('20:30'));
        $topic2->setEndTime(new \DateTime('21:30'));
        $topic2->setState('open');
        $topic2->setCategory($category2);
        $topic2->addRequiredTopic($topic1);
        
        $topic4 = new Topic();
        $topic4->setTitle('Lindy intermediaire');
        $topic4->setDescription('Cours de lindy');
        $topic4->setDay(1);
        $topic4->setType('couple');
        $topic4->setStartTime(new \DateTime('21:30'));
        $topic4->setEndTime(new \DateTime('22:30'));
        $topic4->setState('open');
        $topic4->setCategory($category2);
        $topic4->addRequiredTopic($topic1);
        
        $topic5 = new Topic();
        $topic5->setTitle('Lindy avance');
        $topic5->setDescription('Cours de lindy');
        $topic5->setDay(1);
        $topic5->setType('couple');
        $topic5->setStartTime(new \DateTime('21:30'));
        $topic5->setEndTime(new \DateTime('22:30'));
        $topic5->setState('open');
        $topic5->setCategory($category2);
        $topic5->addRequiredTopic($topic1);
        
        $topic3 = new Topic();
        $topic3->setTitle('Troupe avancee');
        $topic3->setDescription('Troupe avancee');
        $topic3->setDay(2);
        $topic3->setType('couple');
        $topic3->setStartTime(new \DateTime('20:30'));
        $topic3->setEndTime(new \DateTime('21:30'));
        $topic3->setState('open');
        $topic3->setCategory($category3);
        $topic3->addRequiredTopic($topic1);
        
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
