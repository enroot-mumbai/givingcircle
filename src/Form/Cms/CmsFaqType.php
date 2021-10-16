<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsFaq;
use App\Entity\Organization\OrgCompany;
use App\Entity\Product\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsFaqType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orgCompany', EntityType::class,[
                'label' => 'label.company',
                'class' => OrgCompany::class,
                'required' => 'true',
                'attr' => [
                    'class' => 'col-sm-4',
                ]
            ])
            ->add('faq', TextType::class,[
                'label' => 'label.faq',
                'attr' => [
                    'class' => 'col-sm-4',
                ]
            ])
            ->add('product', EntityType::class,[
                'label' => 'label.product',
                'class' => Product::class,
                'placeholder' => 'help.form.select',
                'required' => false,
                'attr' => [
                    'class' => 'col-sm-4',
                ]
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
            'data_class' => CmsFaq::class,
        ]);
    }
}
