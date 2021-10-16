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

class TrnFundRaiserCircleEventDetailsPortalType extends AbstractType
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
                    'class' => 'longtextarea',
                    'placeholder' => 'label.purpose_of_raising_funds'
                ]
            ])
            ->add('targetAmount', NumberType::class,[
                'label' => 'label.target_amount_name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'label.target_amount_name'
                ]
            ])
            ->add('mstCurrencyTargetAmount', EntityType::class, [
                'class' => MstCurrency::class,
//                'placeholder' => 'placeholder.form.select',
                'label' => 'label.target_currency_name',
                'required' => true,
                'choice_label' => 'iso3',
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where("c.isActive = :isActive")
                        ->setParameter('isActive', 1);
                }
            ])
            ->add('isUrgent', CheckboxType::class,[
                'label' => false,
//                'value'=> 0,
//                'mapped' => false,
                'required'=> false,
                'help' => 'Select if event is Urgent'
            ])
            /*->add('minContributionAmount', NumberType::class,[
                'label' => 'label.min_amount_name',
                'required' => false,
                'attr' => [
                    'placeholder' => 'label.min_amount_name',
                ]
            ])
            ->add('mstCurrencyMinContribution', EntityType::class, [
                'class' => MstCurrency::class,
                'choice_label' => 'iso3',
//                'placeholder' => 'placeholder.form.select',
                'label' => 'label.min_currency_name',
                'required' => true,
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where("c.isActive = :isActive")
                        ->setParameter('isActive', 1);
                }
            ])*/
            ->add('mstEventOccurrence', EntityType::class, [
                'class' => MstEventOccurrence::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.event_occurrence',
                'required' => true,
                'attr' => [
                    'style' => 'display:none;'
                ]
            ])
            ->add('fromDate', DateType::class,[
                'label' => 'label.from_date',
                'widget' => 'single_text',
                'required' => true,
                'html5' => false,
                'empty_data' => '',
                'attr' => array(
                    'autocomplete' => 'off',
//                    'min' => date('Y-m-d'),
                    'class' => 'datepicker',
                    'placeholder'=>'label.date_format_yyyy_mm_dd',

                )

            ])
            ->add('toDate', DateType::class,[
                'label' => 'label.to_date',
                'widget' => 'single_text',
                'required' => true,
                'html5' => false,
                'empty_data' => '',
                'attr' => array(
                    'autocomplete' => 'off',
//                    'min' => date('Y-m-d'),
                    'class' => 'datepicker',
                    'placeholder'=>'label.date_format_yyyy_mm_dd',
                )
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
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnFundRaiserCircleEventDetails::class,
        ]);
    }
}
