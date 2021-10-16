<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstUploadDocumentType;
use App\Entity\Transaction\TrnCircleEventUploadedDocuments;
use App\Repository\Master\MstUploadDocumentTypeRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\File;

class TrnCircleEventUploadedDocumentsType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mstUploadDocumentType', EntityType::class, [
                'class' => MstUploadDocumentType::class,
                'query_builder' => function (MstUploadDocumentTypeRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where('u.isActive = 1 ');
                },
                'placeholder' => 'placeholder.form.select',
                'required' => false,
                'label' => 'label.media_file_type',
                'attr' => [
                    'class' => 'uploadDocumentType',
                    'index' => '__index__',
                ]
            ])
            ->add('uploadedFilePath', FileType::class,[
                'mapped' => false,
                'required' => false,
                'label' => 'label.media_file_path',
                'attr' =>[
                    'class' => 'custom-file-input'
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
            ->add('mediaFileName', TextType::class,[
                'label' => 'label.media_file_name'
            ])
            ->add('mediaName', TextType::class,[
                'label' => 'label.media_name',
                'required' => true
            ])
            ->add('mediaAltText', TextType::class,[
                'label' => 'label.media_alt_text'
            ])
            ->add('mediaTitle', TextType::class,[
                'label' => 'label.media_title'
            ])
            ->add('mediaURL', TextType::class,[
                'label' => 'label.media_url'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnCircleEventUploadedDocuments::class,
        ]);
    }
}
