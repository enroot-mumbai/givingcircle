<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppModule;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppModuleAccessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('moduleName', TextType::class,[
                'label' => 'label.module.moduleName',
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

            ->add('appusertype', EntityType::class,[
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
            'data_class' => AppModule::class,
        ]);
    }
}
