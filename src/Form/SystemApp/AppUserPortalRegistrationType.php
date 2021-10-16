<?php

namespace App\Form\SystemApp;

use App\Entity\Master\MstStatus;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\SystemApp\AppUserInfo;
use App\Form\Transaction\TrnBankDetailsType;
use App\Form\Transaction\TrnOrganizationDetailsType;
use App\Repository\SystemApp\AppRoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserPortalRegistrationType  extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('appUserInfo', AppUserInfoPortalRegistrationType::class,[
                'label' => false,
                'required' => true
            ])
            ->add('UserPassword', PasswordType::class,[
                'label' => 'label.password',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add('userName', TextType::class,[
                'label' => 'label.username',
                'required' => false
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