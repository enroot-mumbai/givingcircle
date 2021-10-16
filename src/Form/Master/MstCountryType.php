<?php

namespace App\Form\Master;

use App\Entity\Master\MstCountry;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MstCountryType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('country', TextType::class,[
                'label' => 'label.country',
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
            ->add('iso2', TextType::class,[
                'label' => 'label.iso2',
                'attr' => [
                    'class' => 'col-sm-1',
                    'maxlength' => '2',
                    'minlength' => '2'
                ]
            ])
            ->add('phoneCode', TextType::class,[
                'label' => 'label.calling_code',
                'attr' => [
                    'class' => 'col-sm-1',
                    'maxlength' => '8',
                    'minlength' => '2'
                ]
            ])
            ->add('capital', TextType::class,[
                'label' => 'label.capital',
                'attr' => [
                    'class' => 'col-sm-2',
                ]
            ])
            ->add('currency', TextType::class,[
                'label' => 'label.currency',
                'attr' => [
                    'class' => 'col-sm-1',
                    'maxlength' => '3',
                    'minlength' => '3'
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
            'data_class' => MstCountry::class,
        ]);
    }
}
