<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppUser;
use App\Form\Transaction\TrnBankDetailsType;
use App\Form\Transaction\TrnVolunterDetailMyAccountType;
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

class AppUserMyAccountAboutType  extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('appUserInfo', AppUserInfoMyAccountType::class,[
                'label' => false,
                'required' => true
            ])
            ->add('trnBankDetails', CollectionType::class, [
                'entry_type' => TrnBankDetailsType::class,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
            ])
            ->add('trnVolunterDetail', TrnVolunterDetailMyAccountType::class,[
                'label' => false,
                'required' => true
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