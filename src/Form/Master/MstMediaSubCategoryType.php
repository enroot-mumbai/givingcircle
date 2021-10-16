<?php

namespace App\Form\Master;

use App\Entity\Master\MstMediaCategory;
use App\Entity\Master\MstMediaSubCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MstMediaSubCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mediaCategory', EntityType::class,[
                'label' => 'label.category',
                'class' => MstMediaCategory::class,
                'placeholder' => 'placeholder.form.select',
                'required' => true,
                'attr' => [
                    'class' => 'col-sm-4'
                ]
            ])
            ->add('mediaSubCategory', TextType::class,[
                'label' => 'label.subcategory',
                'required' => true,
                'attr' => [
                    'class' => 'col-sm-4'
                ]
            ])
            ->add('sequenceNo', TextType::class,[
                'required' => true,
                'label' => 'label.seq_no',
                'attr' => [
                    'class' => 'col-sm-1',
                ]
            ])
            ->add('isActive', CheckboxType::class,[
                'label' => 'label.is_active',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MstMediaSubCategory::class,
        ]);
    }
}
