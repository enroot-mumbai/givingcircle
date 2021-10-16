<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsBanner;
use App\Entity\Cms\CmsPage;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CmsBannerType extends AbstractType
{
    private $commonHelper;
    public function __construct(CommonHelper $commonHelper)
    {
        $this->commonHelper = $commonHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cmsPage', EntityType::class,[
                'label' => 'label.page',
                'class' => CmsPage::class,
                'data' => $options['data']->getCmsPage(),
            ])
            ->add('bannerName', TextType::class,[
                'label' => 'label.banner',
                'attr' => [
                    'maxlength' => '150'
                ]
            ])
            ->add('bannerUrl', TextType::class,[
                'label' => 'label.target_url',
                'required' => false,
            ])
            ->add('bannerDescription', TextareaType::class,[
                'label' => 'label.description',
                'required'=> false,
                'attr' => [
                    'class' => 'textarea',
                ]
            ])
            ->add('bannerValidFromDate', DateType::class,[
                'label' => 'label.valid_from_date',
                'years' => range(date('Y'), date('Y')),
            ])
            ->add('bannerValidToDate', DateType::class,[
                'label' => 'label.valid_to_date',
                'years' => range(date('Y'), date('Y')+2),
            ])
            ->add('bannerMediaType', ChoiceType::class,[
                'label' => 'label.type',
                'choices' => $this->commonHelper->mediaType(),
                'data' => 'image',
                'required' => true,
            ])
            ->add('bannerDesktopImage', FileType::class,[
                'mapped' => false,
                'required' => $options['image_required'],
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
            ->add('bannerTabletImage', FileType::class,[
                'mapped' => false,
                'required' => $options['image_required'],
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
            ->add('bannerMobileImage', FileType::class,[
                'mapped' => false,
                'required' => $options['image_required'],
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
            ->add('bannerImageSetName', TextType::class,[
                'label' => 'label.file_name',
                'required'=> false,
                'help' => 'Only image name without file extension.'
            ])

            ->add('bannerImageAlt', TextType::class,[
                'label' => 'label.alt',
                'required'=> false,
            ])

            ->add('bannerImageTitle', TextType::class,[
                'label' => 'label.image_title',
                'required'=> false,
            ])
            ->add('bannerVideo', TextType::class,[
                'label' => 'label.video',
                'required'=> false,
            ])

            ->add('bannerVideoPath', TextType::class,[
                'label' => 'label.embedded_url',
                'required'=> false,
            ])
            ->add('sequenceNo', TextType::class,[
                'required' => true,
                'label' => 'label.seq_no',
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])
            ->add('bannerPosition', ChoiceType::class,[
                'label' => 'label.media_display_position',
                'choices' => $this->commonHelper->bannerPosition(),
                /*'placeholder' => 'placeholder.form.select',*/
                'required' => true,
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsBanner::class,
            'image_required' => false,
        ]);
        $resolver->addAllowedValues('image_required', array(true,false));
    }
}
