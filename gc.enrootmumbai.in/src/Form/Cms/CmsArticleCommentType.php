<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsArticle;
use App\Entity\Cms\CmsArticleComment;
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

class CmsArticleCommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('cmsArticle', EntityType::class,[
//                'label' => 'label.article',
//                'required' => 'true',
//                'data' => $options['data']->getCmsArticle(),
//                'class' => CmsArticle::class,
//                'attr' => [
//                    'readonly' => 'readonly'
//                ]
//            ])
            ->add('articleComment', TextareaType::class,[
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
//            ->add('commentDateTime', DateTimeType::class,[
//                'label'=>"Comment Creation Date",
//                'required'=> false,
//                'placeholder' => [
//                    'year' => 'Year',
//                    'month' => 'Month',
//                    'day' => 'Day',
//                    'hour' => 'Hour',
//                    'minute' => 'Minute',
//                    'second' => 'Second',
//                ],
//            ])
            ->add('isApproved', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_approved',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsArticleComment::class,
        ]);
    }
}
