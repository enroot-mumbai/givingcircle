<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstEmploymentStatus;
use App\Entity\Master\MstGender;
use App\Entity\Master\MstMaritalStatus;
use App\Entity\Master\MstSourceOfInformation;
use App\Entity\Transaction\TrnVolunterDetail;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TrnVolunterDetailMyAccountType extends AbstractType
{
    private $commonHelper;
    public function __construct(CommonHelper $commonHelper)
    {
        $this->commonHelper = $commonHelper;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('educationLevel', TextType::class,[
                'label' => 'label.education_level',
                'required' => false
            ])
            ->add('distanceWillingToTravel', ChoiceType::class,[
                'choices' => $this->commonHelper->distanceData(),
                'required' => false,
            ])
            ->add('hasDisability', CheckboxType::class,[
                'label' => 'label.has_disability',
                'required' => false,
                'attr' => [
                    'name' => 'customRadioInline1'
                ]
            ])
            ->add('isWillingToHelpInDisaster', CheckboxType::class,[
                'label' => 'label.has_disability',
                'required' => false,
                'attr' => [
                    'checked' => 'checked'
                ]
            ])
            ->add('mstGender', EntityType::class, [
                'class' => MstGender::class,
                'label' => 'label.gender',
                'required' => false
            ])
            ->add('mstMaritalStatus', EntityType::class, [
                'class' => MstMaritalStatus::class,
                'label' => 'label.marital_status',
                'required' => false
            ])
            ->add('mstEmploymentStatus', EntityType::class, [
                'class' => MstEmploymentStatus::class,
                'label' => 'label.employment_status',
                'required' => false
            ])
            ->add('mstSourceOfInformation', EntityType::class, [
                'class' => MstSourceOfInformation::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.source_of_information',
                'required' => false,
                'multiple' => true,
                'choice_attr' => function($val, $key, $index) {
                    return array('required' => true);
                },
            ])
        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnVolunterDetail::class,
        ]);
    }
}