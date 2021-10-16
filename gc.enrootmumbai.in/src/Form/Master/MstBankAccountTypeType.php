<?php

namespace App\Form\Master;

use App\Entity\Master\MstBankAccountType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MstBankAccountTypeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankAccountType', TextType::class,[
                'label' => 'label.bank_account_type',
                'required' => true,
                'attr' => [
                    'class' => 'col-sm-4'
                ]
            ])
            ->add('isActive', CheckboxType::class,[
                'label' => 'label.is_active',
                'required' => false,
                'attr' => [
                    'checked' => 'checked'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MstBankAccountType::class,
        ]);
    }
}
