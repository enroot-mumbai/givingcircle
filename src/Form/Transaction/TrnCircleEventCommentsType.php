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

class TrnCircleEventCommentsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('comment', TextareaType::class,[
                'label' => 'label.comment',
                'required' => 'true'
            ])
            ->add('commentorName', TextType::class,[
                'label' => 'label.name',
                'required' => true
            ])
            ->add('commentorEmail', EmailType::class,[
                'label' => 'label.email',
                'empty_data' => '',
                'required' => false
            ])
            ->add('commentorWebsite', UrlType::class,[
                'label' => 'label.website',
                'required' => false
            ])
            ->add('mstRating', EntityType::class,[
                'label' => 'label.rating',
                'class' => MstRating::class,
                'required' => false,
                'placeholder' => 'placeholder.form.select'
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
