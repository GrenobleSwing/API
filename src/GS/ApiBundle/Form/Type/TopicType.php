<?php

namespace GS\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use GS\ApiBundle\Entity\Topic;
use GS\ApiBundle\Form\Type\AddressType;
use GS\ApiBundle\Form\Type\ScheduleType;

class TopicType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('activity', EntityType::class, array(
                    'class' => 'GSApiBundle:Activity',
                    'choice_label' => 'title',
                    'position' => 'first',
                ))
                ->add('category')
//                A ameliorer pour ne prendre en compte que ceux de l'annee
                ->add('requiredTopics', EntityType::class, array(
                    'class' => 'GSApiBundle:Topic',
                    'choice_label' => 'title',
                    'multiple' => true,
                ))
                ->add('title', TextType::class, array(
                    'label' => 'Titre',
                ))
                ->add('description', TextareaType::class, array(
                    'label' => 'Description',
                ))
                ->add('type', ChoiceType::class, array(
                    'label' => 'Type de cours',
                    'choices' => array(
                        'Solo' => 'solo',
                        'Couple' => 'couple',
                    ),
                ))
                ->add('address', AddressType::class, array(
                    'label' => 'Adresse',
                ))
                ->add('schedules', CollectionType::class, array(
                    'label' => 'Planning',
                    'entry_type' => ScheduleType::class,
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

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $topic = $event->getData();
            $form = $event->getForm();

            if (null !== $topic && null !== $topic->getActivity()) {
                $this->disableField($form->get('activity'));
                $form->remove('category');
//                A ameliorer pour ne prendre en compte que ceux de l'annee
                $form->add('category', EntityType::class, array(
                    'class' => 'GSApiBundle:Category',
                    'choice_label' => 'name',
                    'position' => array('after' => 'activity'),
                    'choices' => $topic->getActivity()->getCategories(),
                ));
            }
        });
    }

    private function disableField(FormInterface $field)
    {
        $parent = $field->getParent();
        $options = $field->getConfig()->getOptions();
        $name = $field->getName();
        $type = get_class($field->getConfig()->getType()->getInnerType());
        $parent->remove($name);
        $parent->add($name, $type, array_merge($options, ['disabled' => true]));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Topic::class,
        ));
    }

}