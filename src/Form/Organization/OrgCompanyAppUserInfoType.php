<?php

namespace App\Form\Organization;

use App\Entity\Master\MstSalutation;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUserInfo;
use App\Repository\Organization\OrgCompanyOfficeRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class OrgCompanyAppUserInfoType extends AbstractType
{
    /**
     * @var OrgCompanyOfficeRepository
     */
    private $orgCompanyOffice;
    /**
     * OrgCompanyAppUserInfoType constructor.
     * @param OrgCompanyOfficeRepository $orgCompanyOfficeRepository
     */
    public function __construct(OrgCompanyOfficeRepository $orgCompanyOfficeRepository)
    {
        $this->orgCompanyOffice = $orgCompanyOfficeRepository;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $factory = $builder->getFormFactory();
        $builder
            ->add('userEmail', EmailType::class,[
                'label' => 'label.email',
                'required' => true
            ])
            ->add('mstSalutation', EntityType::class,[
                'class' => MstSalutation::class,
                'label' => 'label.salutation',
                'placeholder' => 'placeholder.form.select',
                'required' => true,
            ])
            ->add('userFirstName', TextType::class,[
                'label' => 'label.firstName',
                'required' => true
            ])
            ->add('userMiddleName', TextType::class,[
                'label' => 'label.middleName',
                'required' => false,

            ])
            ->add('userLastName', TextType::class,[
                'label' => 'label.lastName',
                'required' => true
            ])
            ->add('userMobileNumber', TelType::class,[
                'label' => 'label.mobile',
                'required' => false,
                'attr' => [
                    'maxlength' => '10',
                    'minlength' => '10'
                ]
            ])
            ->add('orgCompany', EntityType::class,[
                'class' => OrgCompany::class,
                'label' => 'label.company',
                'data' => $options['data']->getorgCompany(),
                'attr' => [
                    'readonly' => true
                ]
            ])
            ->add('orgCompanyOffice', ChoiceType::class,[
                'choices' => $this->orgCompanyOffice->findBy(['orgCompany' => $options['data']->getorgCompany()]),
                'choice_label' => 'office',
                'choice_value' => 'Id',
                'label' => 'label.company_office',
                'required' => true
            ])
        ;
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppUserInfo::class,
        ]);
    }
}
