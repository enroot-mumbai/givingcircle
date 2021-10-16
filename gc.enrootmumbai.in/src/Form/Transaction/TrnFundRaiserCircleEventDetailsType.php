<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstEventOccurrence;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstPlaceOfWork;
use App\Entity\Master\MstRecurringBy;
use App\Entity\Master\MstSkillSet;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnFundRaiserCircleEventDetails;
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

class TrnFundRaiserCircleEventDetailsType extends AbstractType
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
            ->add('purposeOfRaisingFunds', TextareaType::class,[
                'label' => 'label.purpose_of_raising_funds',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('causeOfFundRaiser', TextareaType::class,[
                'label' => 'label.cause_of_fundraiser',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('tellAStory', TextareaType::class,[
                'label' => 'label.tell_a_story',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('forWhomRaisingFundFor', TextareaType::class,[
                'label' => 'label.for_whom_raising_fund_for',
                'required' => true,
                'attr' => [
                    'class' => 'textarea'
                ]
            ])
            ->add('targetAmount', NumberType::class,[
                'label' => 'label.target_amount_name',
                'required' => true
            ])
            ->add('mstCurrencyTargetAmount', EntityType::class, [
                'class' => MstCurrency::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.target_currency_name',
                'required' => true
            ])
            /*->add('minContributionAmount', NumberType::class,[
                'label' => 'label.min_amount_name',
                'required' => false
            ])
            ->add('mstCurrencyMinContribution', EntityType::class, [
                'class' => MstCurrency::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.min_currency_name',
                'required' => false
            ])*/
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
            ->add('trnFundRaiserCircleEventSubEvents', CollectionType::class, [
                'entry_type' => TrnFundRaiserCircleEventSubEventsType::class,
                'required' => false,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
            ])
            ->add('isDistributedEvent', CheckboxType::class,[
                'label' => 'label.is_distributed_event',
                'required' => false,
                'attr' => [
                    'checked' => false
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnFundRaiserCircleEventDetails::class,
        ]);
    }
}
