<?php

namespace App\Form\Master;

use App\Entity\Master\MstPaymentGateway;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MstPaymentGatewayType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentGateway', TextType::class,[
                'label' => 'label.payment_gateway',
                'required' => true,
                'attr' => [
                    'class' => 'col-sm-4'
                ]
            ])
            ->add('mstPaymentGatewayDetails', CollectionType::class,[
                'label'=>false,
                'entry_type' => MstPaymentGatewayDetailType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
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
            'data_class' => MstPaymentGateway::class,
        ]);
    }
}
