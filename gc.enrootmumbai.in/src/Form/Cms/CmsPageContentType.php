<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsPageContent;
use App\Form\Media\MdaMediaCollectionType;
use App\Service\CommonHelper;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CmsPageContentType extends AbstractType
{
    private $commonHelper;
    public function __construct(CommonHelper $commonHelper)
    {
        $this->commonHelper = $commonHelper;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('pageContent', TextareaType::class,[
                'label' => 'label.content_block',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('mediaType', ChoiceType::class,[
                'label' => 'label.type',
                'choices' => $this->commonHelper->mediaType(),
                'placeholder' => 'placeholder.form.select',
                'required' => false,
                'empty_data' => '',
            ])
            ->add('mediaName', TextType::class,[
                'label' => 'label.name',
                'required' => false,
            ])
            ->add('mediaPath', TextType::class,[
                'label' => 'label.embedded_url',
                'required' => false,
                'help' => 'Please add the Url in case of external video'
            ])
            ->add('mediaFileName', FileType::class,[
                'mapped' => false,
                'label' => 'Upload File',
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

                ]
            ])
            ->add('mediaAlText', TextType::class,[
                'label' => 'label.alt',
                'required' => false
            ])
            ->add('mediaTitle', TextType::class,[
                'label' => 'label.title',
                'required' => false
            ])
            ->add('position', NumberType::class,[
                'label' => 'label.position',
                'required' => false
            ])
            ->add('removeContentImage', CheckboxType::class,[
                'mapped' => false,
                'required'=> false,
                'label' => 'label.delete_image',
                'help' => 'Select if you do not need the content block image'
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])
            ->add('mediaPosition', ChoiceType::class,[
                'label' => 'label.media_display_position',
                'choices' => $this->commonHelper->mediaPosition(),
                'placeholder' => 'placeholder.form.select',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsPageContent::class,
        ]);
    }
}
