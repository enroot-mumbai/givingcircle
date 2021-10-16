<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstCurrency;
use App\Entity\Master\MstSalutation;
use App\Entity\Transaction\TrnCrowdFundEvent;
use App\Entity\Transaction\TrnCrowdFundEventOfflineTransfer;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
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

class TrnCrowdFundEventOfflineTransferType extends AbstractType
{
    /**
     * @var MstCountryRepository
     */
    private $mstCountryRepository;

    /**
     * constructor
     * @param MstCountryRepository $mstCountryRepository
     */
    public function __construct(MstCountryRepository $mstCountryRepository)
    {
        $this->mstCountryRepository = $mstCountryRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('bankTransactionId', TextType::class,[
                'label' => false,
                'required' => true,
                'attr' => [
//                    'placeholder' => 'Bank Transaction Id'
                ]
            ])
            ->add('amountDonated', NumberType::class,[
                'label' => false,
                'required' => true,
                'attr' => [
//                    'placeholder' => 'Amount Donated'
                ]
            ])
            ->add('mstSalutation', EntityType::class, [
                'class' => MstSalutation::class,
                'label' => false,
                'required' => true,
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where("a.isActive = :active")
                        ->setParameter('active', 1);
                },
                'attr' => ['class' => 'form-control sm']
            ])
            ->add('firstName', TextType::class,[
                'label' => false,
                'attr' => [
//                    'placeholder' => 'First Name'
                ]
            ])
            ->add('lastName', TextType::class,[
                'label' => false,
                'attr' => [
//                    'placeholder' => 'Last Name'
                ]
            ])
            ->add('email', EmailType::class,[
                'label' => false,
                'attr' => [
//                    'placeholder' => 'Email Id'
                ]
            ])
            ->add('mobileCountryCode', ChoiceType::class,[
                'label' => false,
                'required' => true,
                "choices" => ['+91' => '+91'],
//                "choices" => $this->mstCountryRepository->getCountryCodes(),
                'attr' => [
                    'maxlength' => 3
                ]
            ])
            ->add('mobileNumber', NumberType::class,[
                'label' => 'label.mobile',
                'attr' => [
                    'maxlength' => 10,
                    'class' => 'mob-no-input'
                ]
            ])
            ->add('isAnonymousDonation', CheckboxType::class,[
                'label' => false,
                'required' => false,
                'attr' => [
                    'checked' => false
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnCrowdFundEventOfflineTransfer::class,
        ]);
    }
}
