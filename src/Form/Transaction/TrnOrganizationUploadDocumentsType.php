<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstUploadDocumentType;
use App\Entity\Transaction\TrnOrganizationUploadDocuments;
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

class TrnOrganizationUploadDocumentsType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mediaFileName', FileType::class,[
                'mapped' => false,
                'required' => $options['file_required'],
                'label' => 'label.media_file_path',
                'attr' =>[
                    'class' => 'custom-file-input'
                ],
                'constraints' => [
                    new File([
                            'maxSize' => '2000k',
                            'maxSizeMessage' => 'The maximum file upload size is 2 mb.',
                            'mimeTypes' => [
                                'application/pdf',
                            ],
                            'mimeTypesMessage' => 'Please upload a valid pdf file.',
                        ]
                    )
                ],
            ])
            ->add('mediaName', TextType::class,[
                'label' => 'label.document_file_name'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnOrganizationUploadDocuments::class,
            'file_required' => false,
        ]);
        $resolver->addAllowedValues('file_required', array(true,false));
    }
}
