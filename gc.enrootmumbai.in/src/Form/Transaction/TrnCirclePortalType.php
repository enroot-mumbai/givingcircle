<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstHighlights;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Form\Master\MstDaysOfWeekType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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

class TrnCirclePortalType extends AbstractType
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
            ->add('circle', TextType::class,[
                'label' => 'label.circle',
                'required' => true,
                'attr' =>[
                    'readonly' => true
                ]
            ])
            ->add('impactStatement', TextareaType::class,[
                'label' => 'label.impact_statement',
                'required' => false,
                'attr' => [
                    'class' => 'longtextarea',
                    'placeholder' => "info.impact_statement"
                ]
            ])
            ->add('circleInformation', TextareaType::class,[
                'label' => 'label.information_about_circle',
                'required' => true,
                'attr' => [
                    'class' => 'longtextarea',
                    'placeholder' => "info.information_about_circle"
                ]
            ])
            ->add('howGoalWillBeAchieved', TextareaType::class,[
                'label' => 'label.how_goal_will_be_achieved',
                'required' => true,
                'attr' => [
                    'class' => 'longtextarea',
                    'placeholder' => "info.how_goal_will_be_achieved"
                ]
            ])
            ->add('suggestedKeywords', TextareaType::class,[
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                    'placeholder' => "info.suggested_keywords"
                ]
            ])
            /*->add('beneficiaryBankName', TextType::class,[
                'label' => 'label.beneficiary_bank_name',
                'required' => false
            ])
            ->add('beneficiaryAccountHolderName', TextType::class,[
                'label' => 'label.beneficiary_account_holder_name',
                'required' => false,
            ])
            ->add('beneficiaryBankAccountNumber', TextType::class,[
                'label' => 'label.beneficiary_bank_account_number',
                'required' => false,
            ])
            ->add('beneficiaryIfscCode', TextType::class,[
                'label' => 'label.beneficiary_ifsc_code',
                'required' => false,
            ])
            ->add('mstBankAccountTypeBeneficiary', EntityType::class, [
                'class' => MstBankAccountType::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.beneficiary_account_type',
                'required' => false,
            ])*/
            ->add('mstCountry', EntityType::class, [
                'class' => MstCountry::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.country',
                'required' => true,
                'attr' => [
                    'class' => 'mstcountry'
                ]
            ])
            ->add('mstJoinBy', EntityType::class, [
                'class' => MstJoinBy::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.type_of_circle',
                'required' => true,
                'attr' => [
                    'style' => 'display:none;'
                ]
            ])
        ;
        $location = function ($form, $data) {
            $formStateOptions = [
                'label' => 'label.state',
                'class' => MstState::class,
                'required' => true,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $country_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $country_id = $data["mstCountry"];
                    } else {
                        $country_id = $data->getMstCountry()?$data->getMstCountry()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCountry = :country')->setParameter('country', $country_id);
                },
            ];
            $formCityOptions = [
                'label' => 'label.city',
                'class' => MstCity::class,
                'required' => true,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $state_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $state_id = $data["mstState"];
                    } else {
                        $state_id = $data->getMstState()?$data->getMstState()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstState = :state')->setParameter('state',
                        $state_id);
                },
            ];
            $form
                ->add('mstState', EntityType::class,$formStateOptions, [
                    'label' => 'label.state',
                ])
                ->add('mstCity', EntityType::class, $formCityOptions, [
                    'label' => 'label.city',
                    'required' => true,
                ])
            ;
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($location) {
            $form = $event->getForm();
            $data = $event->getData();
            $location ($form, $data);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($location) {
            $form = $event->getForm();
            $data = $event->getData();
            if (array_key_exists('mstCountry', $data)) {
                $location($form, $data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnCircle::class,
        ]);
    }
}
