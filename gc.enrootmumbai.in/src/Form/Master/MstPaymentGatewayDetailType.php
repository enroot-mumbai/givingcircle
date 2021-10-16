<?php

namespace App\Form\Master;

use App\Entity\Master\MstPaymentGatewayDetail;
use App\Service\CommonHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MstPaymentGatewayDetailType extends AbstractType
{
    private $commonHelper;
    public function __construct(CommonHelper $commonHelper)
    {
        $this->commonHelper = $commonHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('paymentGatewayEnv', ChoiceType::class,[
                'label' => 'label.environment',
                'choices' => $this->commonHelper->environmentType(),
            ])
            ->add('paymentKey', TextType::class,[
                'label' => 'label.key',
                'required' => true,
            ])
            ->add('paymentKeyValue', TextType::class,[
                'label' => 'label.value',
                'required' => true,
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
            'data_class' => MstPaymentGatewayDetail::class,
        ]);
    }
}
