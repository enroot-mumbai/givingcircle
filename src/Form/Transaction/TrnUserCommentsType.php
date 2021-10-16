<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEventComments;
use App\Entity\Master\MstRating;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TrnUserCommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class,[
                'label' => 'label.comment',
                'required' => 'true',
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 1000,
                    ]),
                ],
                'attr' => [
                    'class' => 'search-input',
                    'placeholder' => 'Write a comment...',
                ]
            ])
            /*->add('commentorName', TextType::class,[
                'label' => 'label.name',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 3,
                        'max' => 120,
                    ]),
                ]
            ])
            ->add('commentorEmail', EmailType::class,[
                'label' => 'label.email',
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new Length([
                        'min' => 10,
                        'max' => 150,
                    ]),
                ]
            ])
            ->add('commentorWebsite', UrlType::class,[
                'label' => 'label.website',
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 7,
                        'max' => 150,
                    ]),
                ]
            ])*/
//            ->add('parentComment', HiddenType::class)
            ->add('parentComment', EntityType::class, [
                'class' => TrnCircleEventComments::class,
                'placeholder'=> 'Select',
                'required' => false,
            ])
            ->add('isApproved', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_approved',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnCircleEventComments::class,
        ]);
    }
}
