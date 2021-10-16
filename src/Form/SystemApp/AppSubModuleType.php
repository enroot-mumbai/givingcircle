<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppModule;
use App\Entity\SystemApp\AppRole;
use App\Entity\SystemApp\AppSubModule;
use App\Repository\SystemApp\AppSubModuleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppSubModuleType extends AbstractType
{
    private $appSubModuleRepository;

    public function __construct(AppSubModuleRepository $appSubModuleRepository)
    {
        $this->appSubModuleRepository = $appSubModuleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('appModule', EntityType::class,[
                'class' => AppModule::class,
                'choice_label' => 'moduleName',
                'choice_value' => 'id',
                'label' => 'label.module',
                'data' => $options['data']->getAppmodule(),
                'attr' => [
                    'class' => 'col-sm-4',
                ]
            ])
            ->add('subModuleName', TextType::class,[
                'label' => 'label.submodule',
                'attr' => [
                    'class' => 'col-sm-4',
                ]
            ])
            ->add('subModuleValue', TextType::class,[
                'label' => 'label.value',
                'attr' => [
                    'class' => 'col-sm-4',
                ]
            ])
            ->add('subModuleParentValue', TextType::class,[
                'required' => false,
                'label' => 'label.submodule.subModuleParentValue',
                'attr' => [
                    'class' => 'col-sm-4',
                ]
            ])
            ->add('subModuleStatic', CheckboxType::class,[
                'required' => false,
                'label' => 'label.submodule.subModuleStatic'
            ])
            ->add('subModuleDisplayMenu', CheckboxType::class,[
                'required' => false,
                'label' => 'label.submodule.subModuleDisplayMenu'
            ])
            ->add('parentId', ChoiceType::class,[
                'choices' => $this->appSubModuleRepository->getSubModuleListByModuleId($options['data']->getAppModule()),
                'required' => false,
                'label' => 'label.submodule.subModuleParentId',
                'attr' => [
                    'class' => 'col-sm-4',
                ]
            ])
            ->add('isChildMenu', CheckboxType::class,[
                'label' => 'label.is_child',
                'required' => false,
            ])
            ->add('sequenceNo', TextType::class,[
                'required' => true,
                'label' => 'label.seq_no',
                'attr' => [
                    'class' => 'col-sm-1',
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
                'label' => 'label.is_active',
                'required' => false,
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
