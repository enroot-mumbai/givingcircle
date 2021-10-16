<?php

namespace App\Form\Organization;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstOfficeCategory;
use App\Entity\Master\MstState;
use App\Entity\Organization\OrgCompany;
use App\Entity\Organization\OrgCompanyOffice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimezoneType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;


class OrgCompanyOfficeType extends AbstractType
{

    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orgCompany', EntityType::class,[
                'label' => 'label.company',
                'class' => OrgCompany::class,
                'required' => true,
                'attr' => [
                    'readonly' => 'true'
                ]
            ])
            ->add('mstOfficeCategory', EntityType::class,[
                'label' => 'label.office_category',
                'class' => MstOfficeCategory::class,
                'required' => true,
            ])
            ->add('office', TextType::class,[
                'label' => 'label.company_office',
                'required' => true,
            ])

            ->add('officeAddressOne', TextType::class,[
                'label' => 'label.addressOne',
                'required' => true,
            ])
            ->add('officeAddressTwo', TextType::class,[
                'label' => 'label.addressTwo',
                'required' => true,
            ])
            ->add('officePincode', TextType::class,[
                'label' => 'label.pincode',
                'required' => true,
            ])
            ->add('mstCountry', EntityType::class, [
                'class' => MstCountry::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.country',
            ])
            ->add('officeTelNumber', TelType::class,[
                'label' => 'label.telephone',
                'required' => true,
                'attr' => [
                    'maxlength' => '10',
                    'minlength' => '10'
                ]
            ])
            ->add('officeFaxNumber', TelType::class,[
                'label' => 'label.fax',
                'required' => false,
                'attr' => [
                    'maxlength' => '10',
                    'minlength' => '10'
                ]
            ])
            ->add('officeEmail', EmailType::class,[
                'label' => 'label.email',
                'required' => true,
            ])
            ->add('officeTimeZone', TimezoneType::class,[
                'label' => 'label.timezone',
                'required' => true,
                'placeholder' => 'placeholder.form.select'
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
                'attr' => [
                    'checked' => 'checked'
                ]
            ])
        ;
        /**
         * @param $form
         * @param $data
         */
        $refreshLocation = function ($form, $data) {
            $formStateOptions = [
                'label' => 'label.state',
                'class' => MstState::class,
                'required' => true,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if( array_key_exists('mstCountry', $data) )
                    {
                        $country_id = $data["mstCountry"];
                    }else{
                        $country_id = $data->getMstCountry()?$data->getMstCountry()->getId():null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCountry =:country')->setParameter('country', $country_id);
                },
            ];
            $formCityOptions = [
                'label' => 'label.city',
                'class' => MstCity::class,
                'required' => true,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if( array_key_exists('mstState', $data) )
                    {
                        $state_id = $data["mstState"];
                    }else{
                        $state_id = $data->getMstState()?$data->getMstState()->getId():null;
                    }
                    return $dr->createQueryBuilder('c')->andWhere('c.mstState =:state')->setParameter('state',$state_id);
                },
            ];
            $form
                ->add('mstState', EntityType::class,$formStateOptions, [
                    'label' => 'label.state',
                ])
                ->add('mstCity', EntityType::class,$formCityOptions, [
                    'label' => 'label.city',
                ]);
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refreshLocation) {
            $form = $event->getForm();
            $data = $event->getData();
            $refreshLocation ($form, $data);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($refreshLocation) {
            $form = $event->getForm();
            $data = $event->getData();
            if (array_key_exists('mstCountry', $data)) {
                $refreshLocation($form, $data);
            }
        });
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrgCompanyOffice::class,
        ]);
    }
}
