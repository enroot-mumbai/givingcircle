<?php

namespace App\Form\SystemApp;

use App\Entity\Master\MstStatus;
use App\Entity\SystemApp\AppUser;
use App\Entity\SystemApp\AppUserCategory;
use App\Entity\SystemApp\AppUserInfo;
use App\Form\Transaction\TrnBankDetailsType;
use App\Form\Transaction\TrnOrganizationDetailsType;
use App\Form\Transaction\TrnVolunterDetailRegistrationType;
use App\Repository\SystemApp\AppRoleRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserRegistrationEditType  extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add('mstStatus', EntityType::class, [
                'class' => MstStatus::class,
                'placeholder' => 'placeholder.form.select',
                'label' => false,
                'required' => true,
                'attr' => [
                    'class' => 'mststatus'
                ]
            ])
            ->add('reasonToReject', TextareaType::class, [
                'label' => false,
                'required' => false,
                'help' => 'Max 10 words allowed',
                'attr' => [
                    'placeholder' => 'Reason to Reject',
//                    'maxlength' => 1000
                ]
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppUser::class,
        ]);
    }
}