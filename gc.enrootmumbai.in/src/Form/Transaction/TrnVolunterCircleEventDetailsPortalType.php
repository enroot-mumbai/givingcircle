<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstEventOccurrence;
use App\Entity\Master\MstPlaceOfWork;
use App\Entity\Transaction\TrnVolunterCircleEventDetails;
use App\Service\CommonHelper;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Master\MstCityRepository;
use App\Repository\Master\MstStateRepository;
use App\Repository\Master\MstSkillSetRepository;

class TrnVolunterCircleEventDetailsPortalType extends AbstractType
{
    /**
     * @var MstStateRepository
     */
    private $mstStateRepository;
    /**
     * @var MstCityRepository
     */
    private $mstCityRepository;
    /**
     * @var MstSkillSetRepository
     */
    private $masterSkillRepository;

    private $commonHelper;

    /**
     * MstCityType constructor.
     * @param MstStateRepository $mstStateRepository
     * @param MstCityRepository $mstCityRepository
     * @param MstSkillSetRepository $masterSkillRepository
     * @param CommonHelper $commonHelper
     */
    public function __construct(MstStateRepository $mstStateRepository,
                                MstCityRepository $mstCityRepository, MstSkillSetRepository $masterSkillRepository,
                                CommonHelper $commonHelper)
    {
        $this->mstStateRepository = $mstStateRepository;
        $this->mstCityRepository = $mstCityRepository;
        $this->masterSkillRepository = $masterSkillRepository;
        $this->commonHelper = $commonHelper;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('workDescription', TextareaType::class,[
                'label' => 'label.work_description',
                'required' => true,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'label.event_work_description'
                ]
            ])
            ->add('keyResponsibility', TextareaType::class,[
                'label' => 'label.key_responsibility',
                'required' => true,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => 'label.event_key_responsibility'
                ]
            ])
            /*->add('targetNumberOfVoluntersRequired', IntegerType::class,[
                'label' => 'label.targetnumber_of_volunters_required',
                'required' => true
            ])*/
            ->add('mstPlaceOfWork', EntityType::class, [
                'class' => MstPlaceOfWork::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.place_of_work',
                'required' => true,
                'attr' => [
                    'style' => 'display:none;'
                ]
            ])
            ->add('mstEventOccurrence', EntityType::class, [
                'class' => MstEventOccurrence::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.event_occurrence',
                'required' => true,
                'attr' => [
                    'style' => 'display:none;'
                ]
            ])
            /*->add('mstSkillSet', EntityType::class,[
                'label' => 'label.skill_set',
                'class' => MstSkillSet::class,
                'placeholder' => 'placeholder.form.select',
                'multiple'=> true,
                'required' => true,
//                'data' => $this->masterSkillRepository->getOneDefault(),
                'attr' => [
                    'class' => 'js-example-basic-single'
                ]
            ])*/
            ->add('fromDate', DateType::class,[
                'label' => 'label.from_date',
                'widget' => 'single_text',
                'html5' => false,
                'required' => true,
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'datepicker',
                    'placeholder'=>'label.date_format_yyyy_mm_dd',
                ]
            ])
            ->add('toDate', DateType::class,[
                'label' => 'label.to_date',
                'widget' => 'single_text',
                'html5' => false,
                'required' => true,
                'empty_data' => null,
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'datepicker',
                    'placeholder'=>'label.date_format_yyyy_mm_dd'
                ]
            ])
            ->add('fromTime', ChoiceType::class,[
                'label' => false,
                'choices' => $this->commonHelper->timeList(),
                'required' => true,
                'attr' => [
                    'min' => '00:00',
                    //'value' => '00:00'
                ],
            ])
            ->add('toTime', ChoiceType::class,[
                'label' => false,
                'choices' => $this->commonHelper->timeList(),
                'required' => true,
                'disabled' => true,
                'attr' => [
                    'min' => '00:00',
                ],
            ])
            /*->add('trnVolunterCircleEventOnSiteAddresses', TrnVolunterCircleEventOnSiteAddressType::class, [
                'data_class' => null,
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnVolunterCircleEventDetails::class,
        ]);
    }
}
