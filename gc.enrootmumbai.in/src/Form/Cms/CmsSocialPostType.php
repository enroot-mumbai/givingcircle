<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsMedia;
use App\Entity\Cms\CmsSocialPost;
use App\Entity\Master\MstMediaCategory;
use App\Entity\Master\MstMediaSubCategory;
use App\Entity\Master\MstSocial;
use App\Entity\Organization\OrgCompany;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CmsSocialPostType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mstSocial', EntityType::class,[
                'label' => 'label.social',
                'class' => MstSocial::class,
                'placeholder' => 'placeholder.form.select',
                'multiple' => true,
                'required' => true,
            ])
            ->add('postLink', TextType::class,[
                'label' => 'label.link',
                'required' => false
            ])
            ->add('postCaption', TextType::class,[
                'label' => 'label.caption',
                'required' => false,
                'help' => 'If the link already has the seo meta tags defined, the entered caption will be ignored.'
            ])
            ->add('postDescription', TextType::class,[
                'label' => 'label.description',
                'required' => false,
                'help' => 'If the link already has the seo meta tags defined, the entered description will be ignored.'
            ])
            ->add('postMessage', TextareaType::class,[
                'label' => 'label.message',
                'required' => true
            ])
//            ->add('postPictureUrl', TextType::class,[
//                'label' => 'label.embedded_url',
//                'required' => false,
//                'help' => 'Please add the Url in case of external image'
//            ])
            ->add('postPicture', FileType::class,[
                'mapped' => false,
                'required' => false,
                'label' => 'Upload File',
                'constraints' => [
                    new File([
                            'maxSize' => '10000k',
                            'maxSizeMessage' => 'The maximum file upload size is 10 mb.',
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsSocialPost::class,
        ]);
    }
}
