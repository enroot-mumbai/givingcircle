<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstSalutation;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Master\MstTypeOfOrganization;
use App\Entity\Transaction\TrnOrganizationDetails;
use App\Form\Master\MstDaysOfWeekType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Repository\Master\MstCityRepository;
use App\Repository\Master\MstStateRepository;
use App\Repository\Master\MstCountryRepository;
use App\Repository\Master\MstStatusRepository;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TrnOrganizationDetailsMyAccountType extends AbstractType
{
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('organizationName', TextType::class,[
                'label' => 'label.organization_name',
                'required' => true,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('mstTypeOfOrganization', EntityType::class, [
                'class' => MstTypeOfOrganization::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.type_of_organization',
                'required' => true,
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('aboutOrganization', TextareaType::class,[
                'label' => 'label.about_organization',
                'required' => true,
                'attr' => [
                    'class' => 'textarea textarea-instru',
                    'placeholder' => 'About Your Organization *',
                    'onkeyup' => 'checkWordLen(this);'
                ]
            ])
            ->add('registrationCertificateTrustDeed', TextType::class,[
                'label' => 'label.registration_certificate_trust_deed',
                'required' => true,
            ])
            ->add('incorporatedOnDate', DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'required' => true,
                //'format' => 'yyyy-mm-dd',
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'datepicker'
                ]
            ])
            ->add('registrationNo80G', TextType::class,[
                'label' => 'label.registration_no_80g',
                'required' => false,
            ])
            ->add('website', TextType::class,[
                'label' => 'label.website',
                'required' => false,
            ])
            ->add('registrationDate80G', DateType::class,[
                'widget' => 'single_text',
                'html5' => false,
                'required' => false,
                //'format' => 'yyyy-mm-dd',
                'attr' => [
                    'autocomplete' => 'off',
                    'class' => 'datepicker'
                ]
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnOrganizationDetails::class,
        ]);
    }
}