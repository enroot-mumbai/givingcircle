<?php

namespace App\Form\Master;

use App\Entity\Master\MstCurrency;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MstCurrencyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('currency', TextType::class,[
                'label' => 'label.currency',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('iso3', TextType::class,[
                'label' => 'label.iso3',
                'attr' => [
                    'class' => 'col-sm-1',
                    'maxlength' => '3',
                    'minlength' => '3'
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
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MstCurrency::class,
        ]);
    }
}
