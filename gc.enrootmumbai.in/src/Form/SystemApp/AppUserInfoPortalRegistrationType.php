<?php

namespace App\Form\SystemApp;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstSalutation;
use App\Entity\Master\MstSkillSet;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Master\MstUserMemberType;
use App\Entity\SystemApp\AppUserInfo;
use App\Entity\Organization\OrgCompany;
use App\Entity\Transaction\TrnOrganizationDetails;
use App\Form\Transaction\TrnOrganizationDetailsType;
use App\Repository\Master\MstCountryRepository;
use App\Repository\Master\MstCityRepository;
use App\Repository\Master\MstStateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;


class AppUserInfoPortalRegistrationType extends AbstractType
{
    private $em;

    /**
     * @var MstStateRepository
     */
    private $mstStateRepository;
    /**
     * @var MstCityRepository
     */
    private $mstCityRepository;
    /**
     * @var MstCountryRepository
     */
    private $mstCountryRepository;

    /**
     * MstCityType constructor.
     * @param MstStateRepository $mstStateRepository
     * @param MstCityRepository $mstCityRepository
     * @param MstCountryRepository $mstCountryRepository
     */
    public function __construct(MstStateRepository $mstStateRepository, MstCityRepository $mstCityRepository,
                                MstCountryRepository $mstCountryRepository)
    {
        $this->mstStateRepository = $mstStateRepository;
        $this->mstCityRepository = $mstCityRepository;
        $this->mstCountryRepository = $mstCountryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mstSalutation', EntityType::class, [
                'class' => MstSalutation::class,
                'placeholder' => '',
                'label' => 'label.salutation',
                'required' => true,
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where("a.isActive = :active")
                        ->setParameter('active', 1);
                },
                'attr' => ['class' => 'form-control sm']
            ])
            ->add('userFirstName', TextType::class,[
                'label' => 'label.firstName',
                'required' => true,
            ])
            ->add('userLastName', TextType::class,[
                'label' => 'label.lastName',
                'required' => true
            ])
            ->add('userEmail', EmailType::class,[
                'label' => 'label.email',
                'required' => true,
                'attr' => [
                    'autocomplete' => 'new-password'
                ]
            ])
            ->add('mobileCountryCode', ChoiceType::class,[
                'label' => 'label.mobile_country_code',
                'required' => true,
                "choices" => $this->mstCountryRepository->getCountryCodes(),
                'attr' => [
                    'maxlength' => 3
                ]
            ])
            ->add('userMobileNumber', NumberType::class,[
                'label' => 'label.mobile',
                'required' => true,
                'attr' => [
                    'maxlength' => 10,
                    "class" => "mob-no-input"
                ]
            ])
            ->add('mstCountry', EntityType::class, [
                'class' => MstCountry::class,
                'placeholder' => '',
                'label' => 'label.country',
                'required' => true,
                'attr' => [
                    'class' => 'mstcountry'
                ]
            ])
        ;
        $location = function ($form, $data) {
            $formStateOptions = [
                'label' => 'label.state',
                'placeholder' => '',
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
                'attr' => [
                    'class' => 'mststate'
                ]
            ];
            $formCityOptions = [
                'label' => 'label.city',
                'placeholder' => '',
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
                'attr' => [
                    'class' => 'mstcity'
                ]
            ];
            $form
                ->add('mstState', EntityType::class,$formStateOptions, [
                    'label' => 'label.state',
                    'required' => true
                ])
                ->add('mstCity', EntityType::class, $formCityOptions, [
                    'label' => 'label.city',
                    'required' => true,
                ]);
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
            'data_class' => AppUserInfo::class,
        ]);
    }
}
