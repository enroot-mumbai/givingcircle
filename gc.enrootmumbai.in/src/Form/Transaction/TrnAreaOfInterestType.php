<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Transaction\TrnAreaOfInterest;
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

class TrnAreaOfInterestType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('areaInterestPrimary', EntityType::class, [
                'class' => MstAreaInterest::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.area_interest_primary',
                'required' => true,
            ])
            ->add('areaInterestSecondary', EntityType::class, [
                'class' => MstAreaInterest::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.area_interest_secondary',
                'required' => true,
                'multiple'=> true,
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnAreaOfInterest::class,
        ]);
    }
}