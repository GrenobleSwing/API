<?php

namespace GS\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\OptionsResolver\OptionsResolver;

use GS\ApiBundle\Entity\Address;

class AddressType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('recipient', TextType::class, array(
                    'label' => 'Destinataire',
                ))
                ->add('street', TextType::class, array(
                    'label' => 'Rue',
                ))
                ->add('zipCode', TextType::class, array(
                    'label' => 'Code postal',
                ))
                ->add('recipient', TextType::class, array(
                    'label' => 'Destinataire',
                ))
                ->add('city', TextType::class, array(
                    'label' => 'Ville',
                ))
                ->add('county', TextType::class, array(
                    'label' => 'Departement',
                ))
                ->add('state', TextType::class, array(
                    'label' => 'Region',
                ))
                ->add('country', CountryType::class, array(
                    'label' => 'Pays',
                ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Address::class,
        ));
    }

}
