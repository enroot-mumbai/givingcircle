<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstUploadDocumentType;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use App\Entity\Transaction\TrnProductMedia;
use App\Repository\Master\MstUploadDocumentTypeRepository;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Constraints\File;

class TrnProductMediaEditType extends AbstractType
{
    private $commonHelper;
    public function __construct(CommonHelper $commonHelper)
    {
        $this->commonHelper = $commonHelper;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orgCompany', EntityType::class, [
                'class' => OrgCompany::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.org_company',
                'required' => true
            ])
            ->add('trnCircle', EntityType::class, [
                'class' => TrnCircle::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.circle',
                'required' => false,
                'help' => 'Select Circle, if you wish to upload Circle related media'
            ])
            ->add('mstAreaInterestPrimary', EntityType::class, [
                'class' => MstAreaInterest::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.area_interest_primary',
                'required' => true,
            ])
            ->add('mstAreaInterestSecondary', EntityType::class, [
                'class' => MstAreaInterest::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.area_interest_secondary',
                'required' => false,
            ])
            ->add('mediaCollection', CollectionType::class,[
                'mapped' => false,
                'label'=>false,
                'entry_type' => TrnProductMediaCollectionType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
            ])
            ->add('mediaType', ChoiceType::class, [
                'label' => 'label.media_type',
                'choices' => $this->commonHelper->mediaType(),
                'required' => true,

            ])
            ->add('mediaFileName', FileType::class,[
                'mapped' => false,
                'required' => false,
                'label' => false,
                'attr' =>[
                    'class' => 'custom-file-input'
                ],
                'constraints' => [
                    new File([
                            'maxSize' => '5000k',
                            'maxSizeMessage' => 'The maximum file upload size is 5 mb.',
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
            ->add('mediaName', TextType::class,[
                'label' => 'label.media_name',
                'required' => false
            ])
            ->add('mediaAltText', TextType::class,[
                'label' => 'label.media_alt_text',
                'required' => false
            ])
            ->add('mediaTitle', TextType::class,[
                'label' => 'label.media_title',
                'required' => false
            ])
            ->add('mediaURL', TextType::class,[
                'label' => 'label.media_url',
                'required' => false,
                'help' => 'In case of external video, please add the video url '
            ])
            ->add('isActive', CheckboxType::class,[
                'label' => 'label.is_active',
                'required' => false,
                'attr' => [
                    'checked' => 'checked'
                ]
            ])

        ;

        ;
        $refreshEvent = function ($form, $data) {
            $formOptions = [
                'label' => 'label.circle_event',
                'class' => TrnCircleEvents::class,
                'required' => false,
                'help' => 'Select Circle Event, if you wish to upload Circle Event related media',
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if( array_key_exists('trnCircle', $data) )
                    {
                        $trncircle_id = $data["trnCircle"];
                    }else{
                        $trncircle_id = $data->getTrnCircle()?$data->getTrnCircle()->getId():null;
                    }
                    return $dr->createQueryBuilder('e')->andWhere('e.trnCircle =:circle')->setParameter('circle', $trncircle_id);
                },
            ];
            $form
                ->add('trnCircleEvents', EntityType::class,$formOptions, [
                ]);
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refreshEvent) {
            $form = $event->getForm();
            $data = $event->getData();
            $refreshEvent ($form, $data);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($refreshEvent) {
            $form = $event->getForm();
            $data = $event->getData();
            if (array_key_exists('trnCircle', $data)) {
                $refreshEvent($form, $data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnProductMedia::class,
            'image_required' => false,
        ]);
        $resolver->addAllowedValues('image_required', array(true,false));
    }
}