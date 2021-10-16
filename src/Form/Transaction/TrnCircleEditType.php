<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnCircle;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TrnCircleEditType extends AbstractType
{
    /**
     */
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnCircle::class,
        ]);
    }
}
