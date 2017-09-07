<?php

namespace GS\ApiBundle\Form\Type;

use libphonenumber\PhoneNumberFormat;
use Misd\PhoneNumberBundle\Form\Type\PhoneNumberType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use GS\ApiBundle\Entity\Society;
use GS\ApiBundle\Form\Type\AddressType;

class SocietyType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('name', TextType::class, array(
                    'label' => 'Nom',
                ))
                ->add('taxInformation', TextType::class, array(
                    'label' => 'SIRET',
                ))
                ->add('vatInformation', TextType::class, array(
                    'label' => 'Numéro de TVA',
                ))
                ->add('address', AddressType::class, array(
                    'label' => 'Adresse',
                ))
                ->add('email', EmailType::class, array(
                    'label' => 'Adresse email',
                    'position' => 'first',
                ))
                ->add('phoneNumber', PhoneNumberType::class, array(
                    'label' => 'Numero de telephone',
                    'default_region' => 'FR',
                    'format' => PhoneNumberFormat::NATIONAL
                ))
                ->add('submit', SubmitType::class)
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Society::class,
        ));
    }

}
