<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsArticle;
use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstArticleCategory;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstState;
use App\Entity\Organization\OrgCompany;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\BirthdayType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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


class CmsArticleType extends AbstractType
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    private function mediaType()
    {
        $media = array (
            'Image' => 'image',
            'Video' => 'video'
        );
        return $media;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orgCompany', EntityType::class,[
                'label' => 'label.company',
                'class' => OrgCompany::class,
                'required' => true
            ])
            ->add('articleCreateDateTime', DateType::class,[
                'label'=>"Article Creation Date",
                'placeholder' => [
                    'year' => 'Year',
                    'month' => 'Month',
                    'day' => 'Day',
                ],
            ])
//            ->add('appUser', EntityType::class,[
//                'label' => 'label.articleby',
//                'class' => AppUser::class,
//                'placeholder' => 'placeholder.form.select',
//                'required' => false
//            ])

            ->add('articleCreator', TextType::class,[
                'label' => 'label.articleby',
                'required' => false,
                'help' => 'Enter on  Behalf of individual / organization name'
            ])

//            ->add('articleCreatorUrl', TextType::class,[
//                'label' => 'label.article_creator_url',
//                'required' => false,
//                'help' => 'Enter the URL on  Behalf of individual / organization'
//            ])

            ->add('mstArticleCategory', EntityType::class,[
                'label' => 'label.article_category',
                'class' => MstArticleCategory::class,
                'required' => true
            ])
            ->add('mstAreaInterest', EntityType::class,[
                'label' => 'label.area_interest',
                'class' => MstAreaInterest::class,
                'placeholder' => 'placeholder.form.select',
                'required' => false
            ])
            ->add('articleFor', TextType::class,[
                'label' => 'label.article_for',
                'required' => false,
                'help' => 'Enter on  Behalf of individual / organization name'
            ])
            ->add('articleTitle', TextType::class,[
                'label' => 'label.title',
                'required' => true,
            ])

            ->add('introMediaType', ChoiceType::class,[
                'label' => 'label.type',
                'choices' => $this->mediaType(),
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

            ->add('articleIntroImage', FileType::class,[
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
                'entry_type' => CmsArticleContentType::class,
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

            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])

            ->add('locationName', TextType::class,[
                'label' => 'label.location',
                'required'=> false,
            ])

            ->add('mstCountry', EntityType::class, [
                'class' => MstCountry::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.country',
                'required' => false
            ])
        ;

        /**
         * @param $form
         * @param $data
         */
        $refreshLocation = function ($form, $data) {
            $formStateOptions = [
                'label' => 'label.state',
                'class' => MstState::class,
                'required' => false,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if( array_key_exists('mstCountry', $data) )
                    {
                        $country_id = $data["mstCountry"];
                    }else{
                        $country_id = $data->getMstCountry()?$data->getMstCountry()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCountry =:country')->setParameter('country', $country_id);
                },
            ];
            $formCityOptions = [
                'label' => 'label.city',
                'class' => MstCity::class,
                'required' => false,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if( array_key_exists('mstState', $data) )
                    {
                        $state_id = $data["mstState"];
                    }else{
                        $state_id = $data->getMstState()?$data->getMstState()->getId():null;
                    }
                    return $dr->createQueryBuilder('c')->andWhere('c.mstState =:state')->setParameter('state',$state_id);
                },
            ];
            $form
                ->add('mstState', EntityType::class,$formStateOptions, [
                    'label' => 'label.state',
                ])
                ->add('mstCity', EntityType::class,$formCityOptions, [
                    'label' => 'label.city',
                ]);
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refreshLocation) {
            $form = $event->getForm();
            $data = $event->getData();
            $refreshLocation ($form, $data);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($refreshLocation) {
            $form = $event->getForm();
            $data = $event->getData();
            if (array_key_exists('mstCountry', $data)) {
                $refreshLocation($form, $data);
            }
        });
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
