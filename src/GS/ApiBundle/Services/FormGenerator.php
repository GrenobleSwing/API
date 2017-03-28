<?php

namespace GS\ApiBundle\Services;

use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use FOS\RestBundle\View\View;

use GS\ApiBundle\Entity\Year;
use GS\ApiBundle\Form\Type\YearType;
use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Form\Type\ActivityType;
use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Form\Type\TopicType;
use GS\ApiBundle\Entity\Category;
use GS\ApiBundle\Form\Type\CategoryType;
use GS\ApiBundle\Entity\Discount;
use GS\ApiBundle\Form\Type\DiscountType;
use GS\ApiBundle\Entity\Registration;
use GS\ApiBundle\Form\Type\RegistrationType;

class FormGenerator
{
    private $router;
    private $formFactory;
    
    public function __construct(RouterInterface $router, FormFactoryInterface $formFactory)
    {
        $this->router = $router;
        $this->formFactory = $formFactory;
    }
    
    public function getYearForm($year = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $year) {
            $year = new Year();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('year' => $year->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(YearType::class, $year, $options);
    }

    public function getYearDeleteForm($year)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_year', array('year' => $year->getId())))
                ->getForm();
        return $form;
    }

    public function getActivityForm($activity = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $activity) {
            $activity = new Activity();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('activity' => $activity->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(ActivityType::class, $activity, $options);
    }
    
    public function getActivityDeleteForm($activity)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_activity', array('activity' => $activity->getId())))
                ->getForm();
        return $form;
    }

    public function getTopicForm($topic = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $topic) {
            $topic = new Topic();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('topic' => $topic->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(TopicType::class, $topic, $options);
    }
    
    public function getTopicDeleteForm($topic)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_topic', array('topic' => $topic->getId())))
                ->getForm();
        return $form;
    }

    public function getCategoryForm($category = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $category) {
            // Should raise an error
            $category = new Category();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('category' => $category->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(CategoryType::class, $category, $options);
    }
    
    public function getCategoryDeleteForm($category)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_category', array('category' => $category->getId())))
                ->getForm();
        return $form;
    }

    public function getDiscountForm($discount = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $discount) {
            // Should raise an error
            $discount = new Discount();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('discount' => $discount->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(DiscountType::class, $discount, $options);
    }
    
    public function getDiscountDeleteForm($discount)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_discount', array('discount' => $discount->getId())))
                ->getForm();
        return $form;
    }

    public function getRegistrationForm($registration = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $registration) {
            // Should raise an error
            $registration = new Registration();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('registration' => $registration->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(RegistrationType::class, $registration, $options);
    }
    
    public function getRegistrationDeleteForm($registration)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_registration', array('registration' => $registration->getId())))
                ->getForm();
        return $form;
    }

    public function getFormView($form, $template = 'form.html.twig')
    {
        $view = View::create($form, 200)
            ->setTemplate("GSApiBundle:Default:" . $template)
            ->setTemplateVar('form')
            ->setFormat('html')
            ;
        return $view;
    }

    public function getTopicFormView($form, $template = 'form.html.twig')
    {
        $view = View::create($form, 200)
            ->setTemplate("GSApiBundle:Topic:" . $template)
            ->setTemplateVar('form')
            ->setFormat('html')
            ;
        return $view;
    }

}