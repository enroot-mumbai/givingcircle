<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsPressRoom;
use App\Entity\Organization\OrgCompany;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Form\CallbackTransformer;

class CmsPressRoomType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orgCompany', EntityType::class,[
                'label' => 'label.company',
                'class' => OrgCompany::class,
                'required' => 'true'
            ])
            ->add('articleDateTime', DateTimeType::class,[
                'label' => 'label.date',
                'years' => range(date('Y')-1, date('Y')+1),
//                'data' => new \DateTime(),
                ])
            ->add('articleHeading', TextType::class,[
                'label' => 'label.title',
                'attr' => [
                    //'class' => 'col-sm-4'
                ]
            ])
            ->add('articleContent', TextareaType::class,[
                'label' => 'label.content',
                'attr' => [
                    'class' => 'textarea'
                ]
                ])
            ->add('articleIntroDesktopImage', FileType::class,[
                'mapped' => false,
                'required' => false,
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
                'attr' => [
                    'placeholder' => 'Upload a file..',

                ],
            ])
            ->add('articleIntroImageSetName', TextType::class,[
                'label' => 'label.file_name',
                'required'=> false,
                'help' => 'Only image name without file extension.'
            ])

            ->add('articleIntroImageAlt', TextType::class,[
                'label' => 'label.alt',
                'required'=> false,
            ])

            ->add('articleIntroImageTitle', TextType::class,[
                'label' => 'label.image_title',
                'required'=> false,
            ])
            ->add('isActive', CheckboxType::class,[
                'label' => 'label.is_active',
                'required' => false,
                'attr' => [
                    'checked' => 'checked'
                ]
            ])
        ;
        $builder->get('articleDateTime')->addModelTransformer(new CallbackTransformer(
            function ($value) {
                if(!$value) {
                    return new \DateTime('now');
                }
                return $value;
            },
            function ($value) {
                return $value;
            }
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsPressRoom::class,
        ]);
    }
}
