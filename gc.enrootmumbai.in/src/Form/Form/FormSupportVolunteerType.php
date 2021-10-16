<?php

namespace App\Form\Form;

use App\Entity\Form\FormSupport;
use App\Repository\Master\MstAreaInterestRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormSupportVolunteerType extends AbstractType
{
    private $areaInterest;

    public function __construct(MstAreaInterestRepository $areaInterest)
    {
        $this->areaInterest = $areaInterest;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('supportForm', HiddenType::class,[
                'label' =>false,
                'data' => 'volunteer'
            ])
            ->add('fullName', TextType::class,[
                'label' =>false,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 6,
                        'max' => 100,
                    ]),
                ]

            ])
            ->add('emailAddress', EmailType::class,[
                'label' =>false,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 50,
                    ]),
                ]
            ])
            ->add('mobileNumber', TelType::class,[
                'label' =>false,
                'required' => true,
                'attr' => [
                    'minlength' => '10',
                    'maxlength' => '10'
                ],
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 10,
                    ]),
                ]
            ])
            ->add('cityName', TextType::class,[
                'label' =>false,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 30,
                    ]),
                ]
            ])
            ->add('interestType', ChoiceType::class,[
                'placeholder' => '',
                'choices' => $this->areaInterest->getAreaInterest(),
                'required' => true
            ])
            ->add('otherInterestType', TextType::class,[
                'mapped' => false,
                'label' =>false,
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 5,
                        'max' => 50,
                    ]),
                ]
            ])
            ->add('messageDetail', TextareaType::class,[
                'label' =>false,
                'required' => false,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 1000,
                    ]),
                ]

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormSupport::class,
        ]);
    }
}
