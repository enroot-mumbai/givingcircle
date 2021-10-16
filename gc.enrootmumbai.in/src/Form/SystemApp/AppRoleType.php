<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppModule;
use App\Entity\SystemApp\AppRole;
use App\Entity\SystemApp\AppSubModule;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppRoleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('roleName', TextType::class, [
                'label' => 'label.name',
                'attr' => ['class' => 'col-sm-3'],
            ])
            ->add('roleDescription', TextType::class, [
                'label' => 'label.description',
                'attr' => ['class' => 'col-sm-3'],
            ])
            ->add('isOrgEnabled', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.enable_for_organization'
            ])
            ->add('appModule', EntityType::class,[
                'label' => 'label.module',
                'class' => AppModule::class,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'help' => 'help.form.select_multi',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('appSubModule', EntityType::class,[
                'label' => 'label.submodule',
                'class' => AppSubModule::class,
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'help' => 'help.form.select_multi',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppRole::class,
        ]);
    }
}
