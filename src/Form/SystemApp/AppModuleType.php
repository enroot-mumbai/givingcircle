<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppModule;
use App\Entity\SystemApp\AppRole;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppModuleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('moduleName', TextType::class,[
                'label' => 'label.module',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('moduleValue', TextType::class,[
                'label' => 'label.value',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('appRole', EntityType::class,[
                'label' => 'label.role',
                'by_reference' => false,
                'class' => AppRole::class,
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
            'data_class' => AppModule::class,
        ]);
    }
}
