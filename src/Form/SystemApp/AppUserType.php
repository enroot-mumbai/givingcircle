<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Repository\SystemApp\AppRoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserType extends AbstractType
{
    private $appRolesRepository;

    public function __construct(AppRoleRepository $appRolesRepository)
    {

        $this->appRolesRepository = $appRolesRepository;

    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', TextType::class,[
                'label' => 'label.username',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('userPassword', PasswordType::class,[
                'label' => 'label.password',
                'required' => $options['password_required'],
                'empty_data'=> '',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('appUserInfo', AppUserInfoType::class,[
                'label' => false,
                'required' => false
            ])
            ->add('appUserCategory', EntityType::class,[
                'label' => 'label.usercategory',
                'class' => AppUserCategory::class,
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('userRole', ChoiceType::class,[
                'choices'       => $this->appRolesRepository->getRoles(),
                'required'      => true,
                'multiple'      => true,
                'expanded'      => true,
                'label'         => 'label.role',
                'attr' => [
                    'class' => 'col-sm-3'
                ]

            ])
            ->add('isActive', CheckboxType::class,[
                'label' => 'label.is_active',
                'required' => false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppUser::class,
            'password_required' => false,
        ]);
        $resolver->addAllowedValues('password_required', array(true,false));
    }
}
