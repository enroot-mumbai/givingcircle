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


class AppUserInfoRegistrationType extends AbstractType
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
            ->add('mstUserMemberType', EntityType::class, [
                'class' => MstUserMemberType::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.user_member_type',
                'required' => true
            ])
            ->add('mstSkillSet', EntityType::class,[
                'label' => 'label.skill_set',
                'class' => MstSkillSet::class,
                'placeholder' => 'placeholder.form.select',
                'multiple'=> true,
                'required' => true
            ])
            /*->add('mstAreaInterest', EntityType::class,[
                'label' => 'label.area_interest',
                'class' => MstAreaInterest::class,
                'placeholder' => 'placeholder.form.select',
                'multiple'=> true,
                'required' => true,
            ])*/
            ->add('mstSalutation', EntityType::class, [
                'class' => MstSalutation::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.salutation',
                'required' => true
            ])
            ->add('userFirstName', TextType::class,[
                'label' => 'label.firstName',
                'required' => true,
            ])
            ->add('userMiddleName', TextType::class,[
                'label' => 'label.middleName',
                'required' => false
            ])
            ->add('userLastName', TextType::class,[
                'label' => 'label.lastName',
                'required' => true
            ])
            ->add('userEmail', EmailType::class,[
                'label' => 'label.email',
                'required' => true
            ])
            ->add('mobileCountryCode', TextType::class,[
                'label' => 'label.mobile_country_code',
                'attr' => [
                    'class' => 'col-sm-3',
                    'maxlength' => 5
                ]
            ])
            ->add('userMobileNumber', NumberType::class,[
                'label' => 'label.mobile',
                'attr' => [
                    'maxlength' => 10
                ]
            ])
            ->add('dateOfBirth', DateType::class,[
                'label' => 'label.date_of_birth',
                'widget' => 'single_text',
                'required' => false
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
                'attr' => [
                    'class' => 'mststate'
                ]
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
                ])
                ->add('pancardNumber', TextType::class,[
                    'label' => 'label.pan_card',
                    'required' => true,
                    'attr' => [
                        'maxlength' => '10',
                        'minlength' => '10'
                    ]
                ])
                /*->add('locationLatitude', TextType::class,[
                    'label' => 'label.latitude',
                    'required' => false,
                ])
                ->add('locationLongitude', TextType::class,[
                    'label' => 'label.longitude',
                    'required' => false,
                ])
                ->add('facebookLink', TextType::class,[
                    'label' => 'label.facebook_link',
                    'required' => false,
                ])
                ->add('googlePlusLink', TextType::class,[
                    'label' => 'label.google_plus_link',
                    'required' => false,
                ])*/
                /*->add('twitterHandleLink', TextType::class,[
                    'label' => 'label.twitter_handle_link',
                    'required' => false,
                ])*/
                ->add('isSubscribedToNewLetter', CheckboxType::class,[
                    'label' => 'label.is_Subscribed_To_NewLetter',
                    'required' => false
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
            'data_class' => AppUserInfo::class,
        ]);
    }
}
