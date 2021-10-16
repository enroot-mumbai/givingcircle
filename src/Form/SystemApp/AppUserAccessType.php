<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppModule;
use App\Entity\SystemApp\AppSubModule;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserAccessType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', TextType::class,[
                'label' => 'label.user.userName',
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('appModules', EntityType::class,[
                'class' => AppModule::class,
                'by_reference' => false,
                'choice_label' => 'moduleName',
                'multiple' => true,
                'expanded' => true,
                'label' => 'label.module.moduleName',

            ])
            ->add('appSubModules', EntityType::class,[
                'class' => AppSubModule::class,
                'by_reference' => false,
                'choice_label' => 'subModuleName',
                'multiple' => true,
                'expanded' => true,
                'label' => 'label.submodule.subModuleName',

            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppUser::class,
        ]);


    }
}
