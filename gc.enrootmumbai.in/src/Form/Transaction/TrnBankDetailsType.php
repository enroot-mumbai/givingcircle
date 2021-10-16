<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstBankAccountType;
use App\Entity\Transaction\TrnBankDetails;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TrnBankDetailsType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankName', TextType::class,[
                'label' => 'label.bank_name',
                'required' => false
            ])
            ->add('accountHolderName', TextType::class,[
                'label' => 'label.account_holder_name',
                'required' => false
            ])
            ->add('accountNumber', TextType::class,[
                'label' => 'label.account_number',
                'required' => false
            ])
            ->add('ifscCode', TextType::class,[
                'label' => 'label.ifsc_code',
                'required' => false
            ])
            ->add('mstBankAccountType', EntityType::class, [
                'class' => MstBankAccountType::class,
                'label' => 'label.bank_account_type',
                'required' => false
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnBankDetails::class,
        ]);
    }
}