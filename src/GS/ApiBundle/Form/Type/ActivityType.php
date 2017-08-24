<?php

namespace GS\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
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
                ->add('membersOnly', ChoiceType::class, array(
                    'label' => "Reservé aux membres de l'association",
                    'choices' => array(
                        "Oui" => true,
                        "Non" => false
                    )
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
