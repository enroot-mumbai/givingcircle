<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstAreasInCity ;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstEventOccurrence;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstPlaceOfWork;
use App\Entity\Master\MstSkillSet;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnVolunterCircleEventOnSiteAddress;
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

class TrnVolunterCircleEventOnSiteAddressType extends AbstractType
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
            ->add('eventOnSiteAddress1', TextType::class,[
                'label' => 'label.addressOne',
                'required' => true
            ])
            ->add('eventOnSiteAddress2', TextType::class,[
                'label' => 'label.addressTwo',
                'required' => false
            ])
            ->add('eventOnSitePinCode', TextType::class,[
                'label' => 'label.pincode',
                'required' => false
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
            ->add('eventOnSiteLocationLatitude', NumberType::class,[
                'label' => 'label.latitude',
                'required' => true,
            ])
            ->add('eventOnSiteLocationLongitude', NumberType::class,[
                'label' => 'label.longitude',
                'required' => true,
            ])
        ;
        $location = function ($form, $data) {
            /*$formStateOptions = [
                'label' => 'label.state',
                'class' => MstState::class,
                'required' => true,
                'attr' => [
                    'class' => 'mststate'
                ],
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $country_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $country_id = $data["mstCountry"];
                    } else {
                        $country_id = $data->getEventOnSiteMstCountry()?$data->getMstCountry()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCountry = :country')->setParameter('country', $country_id);
                },
            ];
            $formCityOptions = [
                'label' => 'label.city',
                'class' => MstCity::class,
                'required' => true,
                'attr' => [
                    'class' => 'mstcity'
                ],
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $state_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $state_id = $data["mstState"];
                    } else {
                        $state_id = $data->getEventOnSiteMstState()?$data->getEventOnSiteMstState()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstState = :state')->setParameter('state',
                        $state_id);
                },
            ];
            $formAreaInCityOptions = [
                'label' => 'label.area_city',
                'class' => MstAreasInCity::class,
                'required' => true,
                'attr' => [
                    'class' => 'mstareaincity'
                ],
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $city_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $city_id = $data["mstCity"];
                    } else {
                        $city_id = $data->getEventOnSiteMstCity()?$data->getEventOnSiteMstCity()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCity = :city')->setParameter('city',
                        $city_id);
                },
            ];
            */
            $formStateOptions = [
                'label' => 'label.state',
                'class' => MstState::class,
                'required' => true,
                'attr' => [
                    'class' => 'mststate'
                ],
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $country_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $country_id = $data["mstCountry"];
                    } else {
                        $country_id = $data["mstCountry"]?$data["mstCountry"]:null;
//                        $country_id = $data->getMstCountry()?$data->getMstCountry()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCountry = :country')->setParameter('country', $country_id);
                },
            ];
            $formCityOptions = [
                'label' => 'label.city',
                'class' => MstCity::class,
                'required' => true,
                'attr' => [
                    'class' => 'mstcity'
                ],
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $state_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $state_id = $data["mstState"];
                    } else {
                        $state_id = $data["mstState"]?$data["mstState"]:null;
//                        $state_id = $data->getMstState()?$data->getMstState()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstState = :state')->setParameter('state',
                        $state_id);
                },
            ];
            $formAreaInCityOptions = [
                'label' => 'label.area_city',
                'class' => MstAreasInCity::class,
                'required' => true,
                'attr' => [
                    'class' => 'mstareaincity'
                ],
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $city_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $city_id = $data["mstCity"];
                    } else {
                        $city_id = $data["mstCity"]?$data["mstCity"]:null;
//                        $city_id = $data->getEventOnSiteMstCity()?$data->getEventOnSiteMstCity()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCity = :city')->setParameter('city',
                        $city_id);
                },
            ];
            $form
                ->add('mstState', EntityType::class,$formStateOptions, [
                    'label' => 'label.state',
                ])
                ->add('mstCity', EntityType::class,$formCityOptions, [
                    'label' => 'label.city',
                ])
                ->add('mstAreasInCity', EntityType::class, $formAreaInCityOptions, [
                    'label' => 'label.area_in_city',
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
            'data_class' => TrnVolunterCircleEventOnSiteAddress::class,
        ]);
    }
}
