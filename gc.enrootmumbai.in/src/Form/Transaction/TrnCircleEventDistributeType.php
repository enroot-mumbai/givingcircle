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

class TrnCircleEventDistributeType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('targetAmount', NumberType::class,[
                'label' => 'label.target_amount',
                'required' => true
            ])
            ->add('mstCurrencyTargetAmount', EntityType::class, [
                'class' => MstCurrency::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.target_currency',
                'required' => true
            ])
            ->add('minContributionAmount', NumberType::class,[
                'label' => 'label.target_amount',
                'required' => true
            ])
            ->add('mstCurrencyMinContribution', EntityType::class, [
                'class' => MstCurrency::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.target_currency',
                'required' => true
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
