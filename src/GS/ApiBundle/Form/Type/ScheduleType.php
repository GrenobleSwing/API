<?php

namespace GS\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use GS\ApiBundle\Entity\Schedule;

class ScheduleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('startDate', DateType::class, array(
                    'label' => 'Date debut',
                ))
                ->add('startTime', TimeType::class, array(
                    'input' => 'datetime',
                    'widget' => 'choice',
                    'minutes' => range(0, 55, 5),
                ))
                ->add('endTime', TimeType::class, array(
                    'input' => 'datetime',
                    'widget' => 'choice',
                    'minutes' => range(0, 55, 5),
                ))
                ->add('endDate', DateType::class, array(
                    'label' => 'Date fin',
                ))
                ->add('frequency', ChoiceType::class, array(
                    'label' => 'Frequence',
                    'choices' => array(
                        'Ponctuel' => 'once',
                        'Hebdomadaire' => 'weekly',
                    )
                ))
                ->add('teachers', TextType::class, array(
                    'label' => 'Profs',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Schedule::class,
        ));
    }

}