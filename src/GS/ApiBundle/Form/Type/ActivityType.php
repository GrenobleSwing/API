<?php

namespace GS\ApiBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use GS\ApiBundle\Entity\Activity;
use GS\ApiBundle\Entity\Registration;

class ActivityType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('year', EntityType::class, array(
                    'class' => 'GSApiBundle:Year',
                    'choice_label' => 'title',
                    'position' => 'first',
                ))
                ->add('title', TextType::class, array(
                    'label' => 'Titre',
                ))
                ->add('description', TextareaType::class, array(
                    'label' => 'Description',
                ))
                ->add('membersOnly', CheckboxType::class, array(
                    'label' => "Reservé aux membres de l'association",
                    'required' => false,
                ))
                ->add('membershipTopic', EntityType::class, array(
                    'label' => "Adhésion (obligatoire) associée a l'activité",
                    'class' => 'GSApiBundle:Topic',
                    'choice_label' => 'title',
                    'choices' => $options['membership_topics'],
                    'placeholder' => "Choissisez l'adhésion obligatoire",
                    'required' => false,
                    'attr' => array(
                        'class' => 'js-select-single',
                    ),
                ))
                ->add('membership', ChoiceType::class, array(
                    'label' => 'Ensemble des adhésions possibles',
                    'choices' => array(
                        "Oui" => true,
                        "Non" => false
                    )
                ))
                ->add('allow2xPayment', CheckboxType::class, array(
                    'label' => 'Autoriser les paiements en 2 fois',
                    'required' => false,
                ))
                ->add('allow3xPayment', CheckboxType::class, array(
                    'label' => 'Autoriser les paiements en 3 fois',
                    'required' => false,
                ))
                ->add('owners', EntityType::class, array(
                    'label' => 'Admins',
                    'class' => 'GSApiBundle:User',
                    'choice_label' => 'email',
                    'multiple' => true,
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('u')
                                ->orderBy('u.username', 'ASC');
                    },
                    'attr' => array(
                        'class' => 'js-select-multiple',
                    ),
                ))
                ->add('triggeredEmails', ChoiceType::class, array(
                    'label' => 'Liste des emails à envoyer',
                    'choices' => array(
                        "Soumission" => Registration::CREATE,
                        "Mise en liste d'attente" => Registration::WAIT,
                        "Validation" => Registration::VALIDATE,
                        "Paiement" => Registration::PAY,
                        "Annulation" => Registration::CANCEL,
                    ),
                    'multiple' => true,
                    'expanded' => true,
                ))
                ->add('submit', SubmitType::class)
        ;

//        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
//            $activity = $event->getData();
//            $form = $event->getForm();
//
//            if (null !== $activity && null !== $activity->getYear()) {
//                $this->disableField($form->get('year'));
//            }
//        });
    }

//    private function disableField(FormInterface $field)
//    {
//        $parent = $field->getParent();
//        $options = $field->getConfig()->getOptions();
//        $name = $field->getName();
//        $type = get_class($field->getConfig()->getType()->getInnerType());
//        $parent->remove($name);
//        $parent->add($name, $type, array_merge($options, ['disabled' => true]));
//    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Activity::class,
        ));

        $resolver->setRequired('membership_topics');
    }

}
