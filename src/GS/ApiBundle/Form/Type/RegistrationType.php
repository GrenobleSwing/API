<?php

namespace GS\ApiBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;

use GS\ApiBundle\Entity\Registration;

class RegistrationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('topic', EntityType::class, array(
                    'class' => 'GSApiBundle:Topic',
                    'choice_label' => 'title',
                    'position' => 'first',
                ))
                ->add('role', ChoiceType::class, array(
                    'label' => 'Role',
                    'choices' => array(
                        'Leader' => 'leader',
                        'Follower' => 'follower',
                    ),
                ))
                ->add('state', ChoiceType::class, array(
                    'label' => 'Etat',
                    'choices' => array(
                        'Soumise' => 'SUBMITTED',
                        'En liste d\'attente' => 'WAITING',
                        'Validee' => 'VALIDATED',
                        'Payee' => 'PAID',
                        'Annulee' => 'CANCELLED',
                        'Partiellement annulee' => 'PARTIALLY_CANCELLED',
                    ),
                ))
                ->add('submit', SubmitType::class)
        ;

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            $registration = $event->getData();
            $form = $event->getForm();
            
            if (null !== $registration && null !== $registration->getTopic()) {
                $this->disableField($form->get('topic'));
                if ('couple' != $registration->getTopic()->getType()) {
                    $form->remove('role');
                }
                if ('DRAFT' == $registration->getState()) {
                    $form->remove('state');
                }
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
            'data_class' => Registration::class,
        ));
    }

}
