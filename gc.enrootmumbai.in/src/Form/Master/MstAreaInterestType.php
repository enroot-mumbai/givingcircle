<?php

namespace App\Form\Master;

use App\Entity\Master\MstAreaInterest;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\File;

class MstAreaInterestType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('areaInterest', TextType::class,[
                'label' => 'label.area_interest',
                'required' => true,
            ])
            ->add('areaInterestDescription', TextType::class,[
                'label' => 'label.description',
                'required' => false,
            ])
            ->add('mstAreaInterestPrimary', EntityType::class,[
                'label' => 'label.primary_area_of_interest',
                'class' => MstAreaInterest::class,
                'placeholder' => 'placeholder.form.select',
                'required' => false,
            ])
            ->add('icon', FileType::class, [
                'mapped' => false,
                'required' => false,
                'label' => 'label.icon',
                'attr' => [
                    'class' => 'custom-file-input col-sm-4'
                ],
                'constraints' => [
                    new File([
                            'maxSize' => '2000k',
                            'maxSizeMessage' => 'The maximum file upload size is 2 mb.',
                            'mimeTypes' => [
                                'image/jpg',
                                'image/png',
                                'image/jpeg'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid jpg or png file.',
                        ]
                    )
                ],
            ])
            ->add('sequenceNo', NumberType::class,[
                'required' => true,
                'label' => 'label.seq_no',
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
            'data_class' => MstAreaInterest::class,
        ]);
    }
}
