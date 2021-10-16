<?php

namespace App\Form\Organization;

use App\Entity\Master\MstCurrency;
use App\Entity\Organization\OrgCompany;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class OrgCompanyType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * OrgCompanyType constructor.
     * @param EntityManagerInterface $em
     */
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
            ->add('company', TextType::class,[
                'label' => 'label.company',
                'help' => 'help.company',
                'required' => true,
            ])
            ->add('companyLogo', FileType::class,[
                'required' => false,
                'mapped' => false,
                'label' => false,
                'constraints' => [
                    new File([
                            'maxSize' => '300k',
                            'maxSizeMessage' => 'The maximum file upload size is 300 kb.',
                            'mimeTypes' => [
                                'image/jpg',
                                'image/png',
                                'image/jpeg'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid jpg or png file.',
                        ]
                    )
                ],
            ])
            ->add('companyFiscalStartMonth', DateType::class,[
                'label' => 'Fiscal Start Month',
                'years' => range(date("Y"), date("Y")),
                'required' => true,
            ])
            ->add('companyFiscalEndMonth', DateType::class,[
                'label' => 'Fiscal End Month',
                'years' => range(date("Y"), date("Y")+1),
                'required' => true,
            ])
            ->add('mstCurrency', EntityType::class,[
                'label' => 'label.currency',
                'class' => MstCurrency::class,
                'placeholder' => 'help.form.select',
                'required' => true,
            ])
            ->add('companyWebsite', TextType::class,[
                'label' => 'label.website',
                'required' => false,
                'help' => 'help.company_website'
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
                'attr' => [
                    'checked' => 'checked'
                ]
            ])
        ;
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => OrgCompany::class,
        ]);
    }
}
