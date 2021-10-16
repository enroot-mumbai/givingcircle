<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsMedia;
use App\Entity\Master\MstMediaCategory;
use App\Entity\Master\MstMediaSubCategory;
use App\Entity\Organization\OrgCompany;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CmsMediaType extends AbstractType
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
                'required' => 'true'
            ])
            ->add('mediaCategory', EntityType::class,[
                'label' => 'label.category',
                'class' => MstMediaCategory::class,
                'required' => false,
                'placeholder' => 'placeholder.form.select'
            ])
            ->add('mediaType', ChoiceType::class,[
                'label' => 'label.type',
                'choices' => $this->commonHelper->mediaType(),
                'placeholder' => 'placeholder.form.select',
                'required' => true,
            ])
            ->add('mediaName', TextType::class,[
                'label' => 'label.name',
                'required' => true
            ])
            ->add('mediaPath', TextType::class,[
                'label' => 'label.embedded_url',
                'required' => false,
                'help' => 'Please add the Url in case of external video'
            ])
            ->add('mediaImage', FileType::class,[
                'mapped' => false,
                'label' => 'Upload File',
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
                'attr' => [
                    'placeholder' => 'Upload a file..',

                ]
            ])
            ->add('mediaTag')


            ->add('mediaDescription', TextType::class,[
                'label' => 'label.description',
                'required' => false
            ])
            ->add('sequenceNo', TextType::class,[
                'required' => true,
                'label' => 'label.seq_no',
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])

        ;

        /**
         * @param $form
         * @param $data
         */
        $refreshCategory = function ($form, $data) {
            $formSubCategoryOptions = [
                'label' => 'label.subcategory',
                'class' => MstMediaSubCategory::class,
                'required' => false,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if( array_key_exists('mediaCategory', $data) )
                    {
                        $category_id = $data["mediaCategory"];
                    }else{
                        $category_id = $data->getMediaCategory()?$data->getMediaCategory()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mediaCategory =:category')->setParameter('category', $category_id);
                },
            ];

            $form
                ->add('mediaSubCategory', EntityType::class,$formSubCategoryOptions, [
                    'label' => 'label.subcategory',
                ]);
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refreshCategory) {
            $form = $event->getForm();
            $data = $event->getData();
            $refreshCategory ($form, $data);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($refreshCategory) {
            $form = $event->getForm();
            $data = $event->getData();

            if (array_key_exists('mediaCategory', $data)) {
                $refreshCategory($form, $data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsMedia::class,
            'image_required' => false,
        ]);
        $resolver->addAllowedValues('image_required', array(true,false));
    }
}
