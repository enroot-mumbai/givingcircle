<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsFaq;
use App\Entity\Cms\CmsFaqDetail;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsFaqDetailType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cmsFaq', EntityType::class,[
                'label' => 'label.faq',
                'class' => CmsFaq::class,
                'data' => $options['data']->getCmsFaq(),
            ])
            ->add('faqQuestion', TextType::class,[
                'label' => 'label.question',
                'required'=> true,
            ])
            ->add('faqAnswer', TextareaType::class,[
                'label' => 'label.answer',
                'required'=> true,
                'attr' => [
                    'class' => 'textarea',
                ]
            ])
            ->add('sequenceNo', TextType::class,[
                'required' => true,
                'label' => 'label.seq_no',
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsFaqDetail::class,
        ]);
    }
}
