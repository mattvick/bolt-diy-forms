<?php

namespace Bolt\Extension\Mattvick\DiyForms\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Choice;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', array(
               'constraints' => array(
                   new NotBlank(),
                   new Length(array('min' => 3)),
               ),
           ))
            ->add('email', 'email', array(
               'constraints' => array(
                   new NotBlank(),
                   new Email(array('checkMX' => true)),
               ),
           ))
            ->add('gender', 'choice', array(
                'choices' => array(
                    'Male' => 'Male', 
                    'Female' => 'Female',
                ),
                'expanded' => true,
                'constraints' => array(
                    new NotBlank(),
                    new Choice(array(
                        'choices' => array(
                            'Male', 
                            'Female',
                        )
                    ))
                )
            ))
            ->add('save', 'submit');
    }

    public function getName()
    {
        return 'contact';
    }
}