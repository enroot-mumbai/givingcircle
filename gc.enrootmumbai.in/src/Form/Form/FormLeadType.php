<?php

namespace App\Form\Form;

use App\Entity\Form\FormLead;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstLeadStatus;
use App\Entity\Master\MstSalutation;
use App\Entity\Master\MstState;
use App\Entity\Organization\OrgCompany;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormLeadType extends AbstractType
{
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
                'placeholder' => 'placeholder.form.select',
                'required' => 'true'
            ])
            ->add('mstSalutation', EntityType::class, [
                'label' => 'label.salutation',
                'class' => MstSalutation::class,
                'placeholder' => 'placeholder.form.select',
                'required' => false
            ])
            ->add('leadFirstName', TextType::class, [
                'label' => 'label.firstName',
                'required' => true
            ])
            ->add('leadMiddleName', TextType::class, [
                'label' => 'label.middleName',
                'required' => false
            ])
            ->add('leadLastName', TextType::class, [
                'label' => 'label.lastName',
                'required' => true
            ])
            ->add('leadPhoneNumber', TelType::class, [
                'label' => 'label.phone',
                'required' => false,
                'attr' => [
                    'maxlength' => '10',
                    'minlength' => '10'
                ]
            ])
            ->add('leadMobileNumber', TelType::class, [
                'label' => 'label.mobile',
                'required' => false,
                'attr' => [
                    'maxlength' => '10',
                    'minlength' => '10'
                ]
            ])
            ->add('leadEmailAddress', EmailType::class, [
                'label' => 'label.email_primary',
                'required' => false
            ])
            ->add('leadDescription', TextareaType::class, [
                'label' => 'label.description',
                'required' => false
            ])
            ->add('mstLeadStatus', EntityType::class, [
                'label' => 'label.lead_status',
                'class' => MstLeadStatus::class,
                'placeholder' => 'placeholder.form.select',
                'required' => true
            ])
            ->add('leadAddressOne', TextType::class, [
                'label' => 'label.addressOne',
                'required' => false,
            ])
            ->add('leadAddressTwo', TextType::class, [
                'label' => 'label.addressTwo',
                'required' => false,
            ])
            ->add('leadPincode', TextType::class, [
                'label' => 'label.pincode',
                'required' => false,
                'attr' => [
                    'maxlength' => '12',
                    'minlength' => '4'
                ]
            ])
            ->add('mstCountry', EntityType::class, [
                'class' => MstCountry::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.country',
                'data' => $options['data']->getMstCountry(),
                'required' => false,
                'attr' => [
                    'class' => 'mstcountry'
                ]
            ]);
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
                    if (array_key_exists('mstCountry', $data)) {
                        $country_id = $data["mstCountry"];
                    } else {
                        $country_id = $data->getMstCountry() ? $data->getMstCountry()->getId() : null;
                    }
                    return $dr->createQueryBuilder('s')->andWhere('s.mstCountry =:country')->setParameter('country', $country_id);
                },
            ];
            $formCityOptions = [
                'label' => 'label.city',
                'class' => MstCity::class,
                'required' => true,
                'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (array_key_exists('mstState', $data)) {
                        $state_id = $data["mstState"];
                    } else {
                        $state_id = $data->getMstState() ? $data->getMstState()->getId() : null;
                    }
                    return $dr->createQueryBuilder('c')->andWhere('c.mstState =:state')->setParameter('state', $state_id);
                },
            ];
            $form
                ->add('mstState', EntityType::class, $formStateOptions, [
                    'label' => 'label.state',
                ])
                ->add('mstCity', EntityType::class, $formCityOptions, [
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
            'data_class' => FormLead::class,
        ]);
    }
}
