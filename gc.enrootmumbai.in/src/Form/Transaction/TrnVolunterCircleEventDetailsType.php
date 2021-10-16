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
use App\Entity\Transaction\TrnVolunterCircleEventDetails;
use App\Form\Master\MstDaysOfWeekType;
use Doctrine\ORM\EntityRepository;
use MongoDB\BSON\Timestamp;
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

class TrnVolunterCircleEventDetailsType extends AbstractType
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
     * MstCityType constructor.
     * @param MstStateRepository $mstStateRepository
     * @param MstCityRepository $mstCityRepository
     */
    public function __construct(MstStateRepository $mstStateRepository, MstCityRepository $mstCityRepository)
    {
        $this->mstStateRepository = $mstStateRepository;
        $this->mstCityRepository = $mstCityRepository;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('workDescription', TextareaType::class,[
                'label' => 'label.work_description',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('keyResponsibility', TextareaType::class,[
                'label' => 'label.key_responsibility',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('targetNumberOfVoluntersRequired', IntegerType::class,[
                'label' => 'label.targetnumber_of_volunters_required',
                'required' => true
            ])
            ->add('mstPlaceOfWork', EntityType::class, [
                'class' => MstPlaceOfWork::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.place_of_work',
                'required' => true,
            ])
            ->add('mstEventOccurrence', EntityType::class, [
                'class' => MstEventOccurrence::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.event_occurrence',
                'required' => true,
            ])
            ->add('mstSkillSet', EntityType::class,[
                'label' => 'label.skill_set',
                'class' => MstSkillSet::class,
                'placeholder' => 'placeholder.form.select',
                'multiple'=> true,
                'required' => true
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
            ->add('fromTime', TimeType::class,[
                'label' => 'label.start_time',
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('toTime', TimeType::class,[
                'label' => 'label.end_time',
                'widget' => 'single_text',
                'required' => true
            ])
            ->add('trnVolunterCircleEventOnSiteAddresses', CollectionType::class, [
                'entry_type' => TrnVolunterCircleEventOnSiteAddressType::class,
                'required' => false,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
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
            ->add('trnVolunterCircleEventSubEvents', CollectionType::class, [
                'entry_type' => TrnVolunterCircleEventSubEventsType::class,
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
            'data_class' => TrnVolunterCircleEventDetails::class,
        ]);
    }
}
