<?php

namespace GS\ApiBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
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
use GS\ApiBundle\Form\Type\DeleteType;
use GS\ApiBundle\Entity\User;
use GS\ApiBundle\Form\Type\UserType;

class FormGeneratorService
{
    private $router;
    private $formFactory;
    private $em;

    public function __construct(RouterInterface $router, FormFactoryInterface $formFactory,
            EntityManagerInterface $em)
    {
        $this->router = $router;
        $this->formFactory = $formFactory;
        $this->em = $em;
    }

    public function getYearForm($year = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $year) {
            $year = new Year();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('year' => $year->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(YearType::class, $year, $options);
    }

    public function getActivityForm($activity = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $activity) {
            $activity = new Activity();
            $options['membership_topics'] = array();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if ($activity->getId() !== null) {
                $options['membership_topics'] = $this->em
                        ->getRepository('GSApiBundle:Topic')
                        ->getMembershipTopicsForActivity($activity);
            }
            elseif ($activity->getYear() !== null)
            {
                $options['membership_topics'] = $this->em
                        ->getRepository('GSApiBundle:Topic')
                        ->getMembershipTopicsForYear($activity->getYear());
            } else {
                $options['membership_topics'] = array();
            }
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('activity' => $activity->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(ActivityType::class, $activity, $options);
    }

    public function getTopicForm($topic = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $topic) {
            $topic = new Topic();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('topic' => $topic->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(TopicType::class, $topic, $options);
    }

    public function getCategoryForm($category = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $category) {
            // Should raise an error
            $category = new Category();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('category' => $category->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(CategoryType::class, $category, $options);
    }

    public function getDiscountForm($discount = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $discount) {
            // Should raise an error
            $discount = new Discount();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('discount' => $discount->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(DiscountType::class, $discount, $options);
    }

    public function getRegistrationForm($registration = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $registration) {
            // Should raise an error
            $registration = new Registration();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('registration' => $registration->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(RegistrationType::class, $registration, $options);
    }

    public function getVenueForm($venue = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $venue) {
            // Should raise an error
            $venue = new Venue();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('venue' => $venue->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(VenueType::class, $venue, $options);
    }

    public function getDeleteForm($entity, $paramName)
    {
        $options = array();
        $options['action'] = $this->router->generate('delete_' . $paramName, array($paramName => $entity->getId()));
        return $this->formFactory->create(DeleteType::class, null, $options);
    }

    public function getAccountForm($account = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $account) {
            // Should raise an error
            $account = new Account();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('account' => $account->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(AccountType::class, $account, $options);
    }

    public function getPaymentForm($payment = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $payment) {
            $payment = new Payment();
            $item = new PaymentItem();
            $payment->addItem($item);
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('payment' => $payment->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(PaymentType::class, $payment, $options);
    }

    public function getUserForm($user = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $user) {
            $user = new User();
            if (null !== $routeName) {
                $route = $this->router->generate($routeName);
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        } else {
            if (null !== $routeName) {
                $route = $this->router->generate($routeName, array('user' => $user->getId()));
                $i = strrpos($route, '/api');
                $options['action'] = substr($route, $i+4);
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(UserType::class, $user, $options);
    }

    public function getFormView($form, $statusCode = 200, $template = 'form.html.twig')
    {
        $view = View::create($form, $statusCode)
            ->setTemplate("GSApiBundle:Default:" . $template)
            ->setTemplateVar('form')
            ->setFormat('html')
            ;
        return $view;
    }

    public function getTopicFormView($form, $statusCode = 200, $template = 'form.html.twig')
    {
        $view = View::create($form, $statusCode)
            ->setTemplate("GSApiBundle:Topic:" . $template)
            ->setTemplateVar('form')
            ->setFormat('html')
            ;
        return $view;
    }

    public function getPaymentFormView($form, $statusCode = 200, $template = 'form.html.twig')
    {
        $view = View::create($form, $statusCode)
            ->setTemplate("GSApiBundle:Payment:" . $template)
            ->setTemplateVar('form')
            ->setFormat('html')
            ;
        return $view;
    }

}
