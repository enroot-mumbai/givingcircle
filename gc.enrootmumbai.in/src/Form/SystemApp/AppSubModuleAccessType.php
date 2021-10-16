<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppSubModule;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AppSubModuleAccessType extends AbstractType
{


    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subModuleName', TextType::class,[
                'label' => 'label.submodule.subModuleName',
                'disabled' => true,
                'attr' => [
                    'class' => 'col-sm-4',
                ]

            ])
            ->add('appuser', EntityType::class,[
                'help' => 'help.form.select_multi',
                'class' => AppUser::class,
                'choice_label' => 'userName',
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'label' => 'label.user',
                'attr' => [
                    'class' => 'col-sm-4 advanced-select',
                ]
            ])
            ->add('appUserType', EntityType::class,[
                'help' => 'help.form.select_multi',
                'class' => AppUserType::class,
                'multiple' => true,
                'expanded' => true,
                'required' => true,
                'label' => 'label.usertype',
                'attr' => [
                    'class' => 'col-sm-4 advanced-select',
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppSubModule::class,
            ]);
    }
}
