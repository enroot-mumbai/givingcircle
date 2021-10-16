<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsArticle;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstArticleCategory;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstState;
use App\Entity\Organization\OrgCompany;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\DateTime;
use Symfony\Component\Validator\Constraints\File;


class CmsArticleBlogType extends AbstractType
{
    private $commonHelper;
    public function __construct(CommonHelper $commonHelper)
    {
        $this->commonHelper = $commonHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orgCompany', EntityType::class,[
                'label' => 'label.company',
                'class' => OrgCompany::class,
                'required' => true
            ])
//            ->add('articleCreateDateTime', DateTimeType::class,[
//                'label'=>"Article Creation Date",
//                'required'=> false,
//                'placeholder' => [
//                    'year' => 'Year',
//                    'month' => 'Month',
//                    'day' => 'Day',
//                    'hour' => 'Hour',
//                    'minute' => 'Minute',
//                    'second' => 'Second',
//                ],
//            ])

            ->add('articleCreator', TextType::class,[
                'label' => 'label.articleby',
                'required' => false,
                'help' => 'Enter on  Behalf of individual / organization name'
            ])

            ->add('mstArticleCategory', EntityType::class,[
                'label' => 'label.article_category',
                'class' => MstArticleCategory::class,
                'required' => true,
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where("a.id = :id")
                        ->setParameter('id', 1);
                }
            ])
            ->add('articleTitle', TextType::class,[
                'label' => 'label.title',
                'required' => true,
            ])

            ->add('introMediaType', ChoiceType::class,[
                'label' => 'label.type',
                'choices' => $this->commonHelper->mediaType(),
                'data' => 'image',
                'required' => true,
            ])

            ->add('articleIntro', TextareaType::class,[
                'label' => 'label.intro',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])

            ->add('articleIntroDesktopImage', FileType::class,[
                'mapped' => false,
                'label' => false,
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

//            ->add('articleIntroTabletImage', FileType::class,[
//                'mapped' => false,
//                'label' => false,
//                'required' => $options['image_required'],
//                'constraints' => [
//                    new File([
//                            'maxSize' => '2000k',
//                            'maxSizeMessage' => 'The maximum file upload size is 2 mb.',
//                            'mimeTypes' => [
//                                'image/jpg',
//                                'image/png',
//                                'image/jpeg'
//                            ],
//                            'mimeTypesMessage' => 'Please upload a valid jpg or png file.',
//                        ]
//                    )
//                ],
//            ])
//
//            ->add('articleIntroMobileImage', FileType::class,[
//                'mapped' => false,
//                'label' => false,
//                'required' => $options['image_required'],
//                'constraints' => [
//                    new File([
//                            'maxSize' => '2000k',
//                            'maxSizeMessage' => 'The maximum file upload size is 2 mb.',
//                            'mimeTypes' => [
//                                'image/jpg',
//                                'image/png',
//                                'image/jpeg'
//                            ],
//                            'mimeTypesMessage' => 'Please upload a valid jpg or png file.',
//                        ]
//                    )
//                ],
//            ])

            ->add('articleIntroImageSetName', TextType::class,[
                'label' => 'label.file_name',
                'required'=> true,
                'help' => 'Only image name without file extension.'
            ])

            ->add('articleIntroImageAlt', TextType::class,[
                'label' => 'label.alt',
                'required'=> true,
            ])

            ->add('articleIntroImageTitle', TextType::class,[
                'label' => 'label.image_title',
                'required'=> true,
            ])
            ->add('removeIntroImage', CheckboxType::class,[
                'mapped' => false,
                'required'=> false,
                'label' => 'label.delete_image',
                'help' => 'Select if you do not need the intro image'
            ])

            ->add('articleIntroVideo', TextType::class,[
                'label' => 'label.video',
                'required'=> false,
            ])

            ->add('articleIntroVideoPath', TextType::class,[
                'label' => 'label.embedded_url',
                'required'=> false,
            ])

            ->add('cmsArticleContent', CollectionType::class,[
                'label'=>false,
                'entry_type' => CmsArticleBlogContentType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
            ])

            ->add('metaTitle', TextType::class,[
                'label' => 'label.meta_title',
                'required'=> false,
            ])

            ->add('seoSchema', TextareaType::class,[
                'label' => 'label.schema',
                'required'=> false,
            ])

            ->add('metaDescription', TextareaType::class,[
                'label' => 'label.meta_description',
                'required'=> false,
            ])

            ->add('metaKeyword', TextareaType::class,[
                'label' => 'label.meta_keyword',
                'required'=> false,
            ])

            ->add('focusKeyPhrase', TextareaType::class,[
                'label' => 'label.focus_key_phrase',
                'required'=> false,
            ])

            ->add('keyPhraseSynonyms', TextareaType::class,[
                'label' => 'label.key_phrase_synonyms',
                'required'=> false,
            ])

            ->add('articleCanonicalUrl', TextareaType::class,[
                'label' => 'label.canonical_url',
                'required'=> false,
            ])

            ->add('ogTitle', TextType::class,[
                'label' => 'label.social_title',
                'required'=> false,
            ])
            ->add('ogDescription', TextareaType::class,[
                'label' => 'label.social_description',
                'required'=> false,
            ])
            ->add('ogType', TextType::class,[
                'label' => 'label.social_type',
                'data' => 'article',
                'required'=> false,
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('ogImage', FileType::class,[
                'mapped' => false,
                'label' => 'Upload Image File',
                'required' => false,
                'constraints' => [
                    new File([
                            'maxSize' => '3000k',
                            'maxSizeMessage' => 'The maximum file upload size is 3 mb.',
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


            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsArticle::class,
            'image_required' => false,
        ]);
        $resolver->addAllowedValues('image_required', array(true,false));
    }
}
