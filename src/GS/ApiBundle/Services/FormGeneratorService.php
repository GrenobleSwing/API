<?php

namespace GS\ApiBundle\Services;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use FOS\RestBundle\View\View;

use GS\StructureBundle\Entity\Account;
use GS\StructureBundle\Entity\Activity;
use GS\StructureBundle\Entity\Category;
use GS\StructureBundle\Entity\Discount;
use GS\StructureBundle\Entity\Payment;
use GS\StructureBundle\Entity\PaymentItem;
use GS\StructureBundle\Entity\Registration;
use GS\StructureBundle\Entity\Topic;
use GS\StructureBundle\Entity\User;
use GS\StructureBundle\Entity\Venue;
use GS\StructureBundle\Entity\Year;
use GS\StructureBundle\Form\Type\AccountType;
use GS\StructureBundle\Form\Type\AccountPictureType;
use GS\StructureBundle\Form\Type\ActivityType;
use GS\StructureBundle\Form\Type\CategoryType;
use GS\StructureBundle\Form\Type\DeleteType;
use GS\StructureBundle\Form\Type\DiscountType;
use GS\StructureBundle\Form\Type\PaymentType;
use GS\StructureBundle\Form\Type\RegistrationType;
use GS\StructureBundle\Form\Type\TopicType;
use GS\StructureBundle\Form\Type\UserType;
use GS\StructureBundle\Form\Type\VenueType;
use GS\StructureBundle\Form\Type\YearType;

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

    public function getActivityForm($activity = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $activity) {
            $activity = new Activity();
            $options['membership_topics'] = array();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            # I think the 2 if could be grouped together keeping only getMembershipTopicsForYear
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
                $options['action'] = $this->router->generate($routeName, array('activity' => $activity->getId()));
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

    public function getDeleteForm($entity, $paramName)
    {
        $options = array();
        $options['action'] = $this->router->generate('gs_api_delete_' . $paramName, array($paramName => $entity->getId()));
        return $this->formFactory->create(DeleteType::class, null, $options);
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

    public function getAccountPictureForm($account = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $account) {
            // Should raise an error
            return null;
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('id' => $account->getId()));
            }
        }
        if (null !== $method) {
            $options['method'] = $method;
        }

        return $this->formFactory->create(AccountPictureType::class, $account, $options);
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

    public function getUserForm($user = null, $routeName = null, $method = null)
    {
        $options = array();
        if (null === $user) {
            $user = new User();
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName);
            }
        } else {
            if (null !== $routeName) {
                $options['action'] = $this->router->generate($routeName, array('user' => $user->getId()));
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

    public function getRegistrationFormView($registration, $form, $statusCode = 200)
    {
        $data = array(
            'form' => $form,
            'registration' => $registration,
        );
        $view = View::create($data, $statusCode)
            ->setTemplate("GSApiBundle:Registration:add.html.twig")
            ->setTemplateVar('data')
            ->setFormat('html')
            ;
        return $view;
    }

}
