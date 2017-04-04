<?php

namespace GS\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use GS\ApiBundle\Entity\Payment;
use GS\ApiBundle\Form\Type\PaymentItemType;

class PaymentType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('state', HiddenType::class, array(
                    'data' => 'PAID',
                ))
                ->add('type', ChoiceType::class, array(
                    'label' => 'Type de cours',
                    'choices' => array(
                        'Virement' => 'TRANFER',
                        'Liquide' => 'CASH',
                        'Cheque' => 'CHECK',
                        'Paypal' => 'PAYPAL',
                        'CB' => 'CARD',
                    ),
                ))
                ->add('date', DateType::class, array(
                    'label' => 'Date du paiement',
                ))
                ->add('items', CollectionType::class, array(
                    'label' => 'Liste des inscriptions',
                    'entry_type' => PaymentItemType::class,
                    'by_reference' => false,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'prototype' => true,
                    'attr' => array(
                        'class' => 'my-selector',
                    ),
                ))
                ->add('submit', SubmitType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Payment::class,
        ));
    }

}
