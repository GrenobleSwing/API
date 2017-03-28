<?php

namespace GS\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use GS\ApiBundle\Entity\Year;

class YearType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('title', TextType::class, array(
                    'label' => 'Titre',
                ))
                ->add('description', TextareaType::class, array(
                    'label' => 'Description',
                ))
                ->add('startDate', DateType::class, array(
                    'label' => 'Date debut',
                ))
                ->add('endDate', DateType::class, array(
                    'label' => 'Date fin',
                ))
                ->add('submit', SubmitType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Year::class,
        ));
    }

}