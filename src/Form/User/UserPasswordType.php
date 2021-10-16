<?php

namespace App\Form\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('currentPassword', PasswordType::class, [
                'constraints' => [
                    new UserPassword(),
                ],
                'label' => 'label.current_password',
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'col-lg-5'
                ],
            ])
            ->add('newPassword', RepeatedType::class, [
                'type' => PasswordType::class,

                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 8,
                        'max' => 24,
                    ]),
                ],
                'first_options' => [
                    'label' => 'label.new_password',
                    'help' => 'Minimum password length should be 8 characters',
                    'attr' => [
                        'class' => 'col-lg-5'
                    ],

                ],
                'second_options' => [
                    'label' => 'label.confirm_new_password',
                    'attr' => [
                        'class' => 'col-lg-5'
                    ],

                ],

            ])
        ;
    }
}
