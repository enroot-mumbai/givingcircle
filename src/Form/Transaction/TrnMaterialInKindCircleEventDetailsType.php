<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstEventOccurrence;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstPlaceOfWork;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Master\MstSkillSet;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnMaterialInKindCircleEventDetails;
use App\Entity\Transaction\TrnMaterialInKindCircleEventSubEvents;
use App\Form\Master\MstDaysOfWeekType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Master\MstCityRepository;
use App\Repository\Master\MstStateRepository;
use App\Repository\Master\MstCountryRepository;
use App\Repository\Master\MstStatusRepository;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TrnMaterialInKindCircleEventDetailsType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('causeOfEvent', TextareaType::class,[
                'label' => 'label.cause_of_event',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('itemsRequired', TextareaType::class,[
                //'label' => 'label.work_description',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('tellAStory', TextareaType::class,[
                //'label' => 'label.work_description',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('mstEventOccurrence', EntityType::class, [
                'class' => MstEventOccurrence::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.event_occurrence',
                'required' => true,
            ])
            ->add('fromDate', DateType::class,[
                'label' => 'label.from_date',
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('toDate', DateType::class,[
                'label' => 'label.to_date',
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('trnCircleEventRecurringDetails', CollectionType::class, [
                'entry_type' => TrnCircleEventRecurringDetailsType::class,
                'required' => false,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
            ])
            ->add('trnMaterialInKindCircleEventSubEvents', CollectionType::class, [
                'entry_type' => TrnMaterialInKindCircleEventSubEventsType::class,
                'required' => false,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
            ])
            ->add('trnMaterialInKindCircleEventCollectionCentres', CollectionType::class, [
                'entry_type' => TrnMaterialInKindCircleEventCollectionCentreType::class,
                'required' => false,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnMaterialInKindCircleEventDetails::class,
        ]);
    }
}
