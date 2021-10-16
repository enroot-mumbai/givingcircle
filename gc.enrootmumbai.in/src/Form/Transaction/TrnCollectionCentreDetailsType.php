<?php

namespace App\Form\Transaction;


use App\Entity\Master\MstAreasInCity;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCollectionCentreDetails;
use App\Form\Master\MstDaysOfWeekType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
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

class TrnCollectionCentreDetailsType extends AbstractType
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
            ->add('orgCompany', EntityType::class, [
                'class' => OrgCompany::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.org_company',
                'required' => true
            ])
            ->add('appUser', EntityType::class, [
                'class' => AppUser::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.app_user',
                'required' => true
            ])
            ->add('trnCircle', EntityType::class,[
                'class' => TrnCircle::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.circle',
                'required' => false,
            ])
            ->add('firstName', TextType::class,[
                'label' => 'label.firstName',
                'required' => true
            ])
            ->add('lastName', TextType::class,[
                'label' => 'label.lastName',
                'required' => true
            ])
            ->add('address1', TextType::class,[
                'label' => 'label.addressOne',
                'required' => true
            ])
            ->add('address2', TextType::class,[
                'label' => 'label.addressTwo',
                'required' => false
            ])
            ->add('pinCode', TextType::class,[
                'label' => 'label.pincode',
                'required' => true
            ])
            ->add('mstCountry', EntityType::class, [
                'class' => MstCountry::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.country',
                'required' => true,
                'attr' => [
                    'class' => 'mstcountry'
                ]
            ])
            ->add('latitude', NumberType::class,[
                'label' => 'label.latitude',
                'required' => true,
            ])
            ->add('longitude', NumberType::class,[
                'label' => 'label.longitude',
                'required' => true,
            ])
            ->add('mstDaysOfWeek', EntityType::class,[
                'label' => 'label.collection_centre_open_on_days',
                'class' => MstDaysOfWeek::class,
                'placeholder' => 'placeholder.form.select',
                'multiple'=> true,
                'required' => true
            ])
            ->add('startTime', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice'
            ])
            ->add('endTime', TimeType::class, [
                'input'  => 'datetime',
                'widget' => 'choice'
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
                ->add('mstStatus', EntityType::class, [
                    'class' => MstStatus::class,
                    'placeholder' => 'placeholder.form.select',
                    'label' => 'label.status',
                    'required' => false,
                    'attr' => [
                        'class' => 'mststatus'
                    ]
                ])
                ->add('isActive', CheckboxType::class,[
                    'label' => 'label.is_active',
                    'required' => false,
                    'attr' => [
                        'checked' => 'checked'
                    ]
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
            'data_class' => TrnCollectionCentreDetails::class,
        ]);
    }
}
