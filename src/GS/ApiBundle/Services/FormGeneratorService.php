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
use GS\ApiBundle\Entity\Venue;
use GS\ApiBundle\Form\Type\VenueType;
use GS\ApiBundle\Entity\Account;
use GS\ApiBundle\Form\Type\AccountType;
use GS\ApiBundle\Entity\Payment;
use GS\ApiBundle\Entity\PaymentItem;
use GS\ApiBundle\Form\Type\PaymentType;

class FormGeneratorService
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

    public function getVenueForm($venue = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $venue) {
            // Should raise an error
            $venue = new Venue();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('venue' => $venue->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(VenueType::class, $venue, $options);
    }
    
    public function getVenueDeleteForm($venue)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_venue', array('venue' => $venue->getId())))
                ->getForm();
        return $form;
    }

    public function getAccountForm($account = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $account) {
            // Should raise an error
            $account = new Account();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('account' => $account->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(AccountType::class, $account, $options);
    }
    
    public function getAccountDeleteForm($account)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_account', array('account' => $account->getId())))
                ->getForm();
        return $form;
    }

    public function getPaymentForm($payment = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $payment) {
            $payment = new Payment();
            $item = new PaymentItem();
            $payment->addItem($item);
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('payment' => $payment->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }
        
        return $this->formFactory->create(PaymentType::class, $payment, $options);
    }
    
    public function getPaymentDeleteForm($payment)
    {
        $form = $this->formFactory->createBuilder()
                ->add('submit', SubmitType::class, array(
                    'label' => 'Confirmer la supression',
                ))
                ->setMethod('DELETE')
                ->setAction($this->router->generate('delete_payment', array('payment' => $payment->getId())))
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

    public function getPaymentFormView($form, $template = 'form.html.twig')
    {
        $view = View::create($form, 200)
            ->setTemplate("GSApiBundle:Payment:" . $template)
            ->setTemplateVar('form')
            ->setFormat('html')
            ;
        return $view;
    }

}
