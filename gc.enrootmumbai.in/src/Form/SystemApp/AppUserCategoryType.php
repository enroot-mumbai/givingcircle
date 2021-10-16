<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppModule;
use App\Entity\SystemApp\AppSubModule;
use App\Entity\SystemApp\AppUserCategory;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userCategory', TextType::class,[
                'label' => 'label.usercategory',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('appModule', EntityType::class,[
                'class' => AppModule::class,
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'label' => 'label.module',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('appSubModule', EntityType::class,[
                'class' => AppSubModule::class,
                'multiple' => true,
                'expanded' => true,
                'label' => 'label.submodule',
                'attr' => [
                    'class' => 'col-sm-3'
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
            'data_class' => AppUserCategory::class,
        ]);
    }
}
