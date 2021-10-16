<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstCurrency;
use App\Entity\Transaction\TrnCrowdFundEvent;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
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

class TrnCrowdFundEventPortalType extends AbstractType
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
            ->add('targetAmount', NumberType::class,[
                'label' => 'label.target_amount_name',
                'required' => true,
                'attr' => [
                    'placeholder' => 'label.target_amount_name'
                ]
            ])
            ->add('mstTargetAmountCurrency', EntityType::class, [
                'class' => MstCurrency::class,
//                'placeholder' => 'placeholder.form.select',
                'label' => 'label.target_currency_name',
                'required' => true,
                'choice_label' => 'iso3',
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where("c.isActive = :isActive")
                        ->setParameter('isActive', 1);
                }
            ])
            ->add('minimumContribution', NumberType::class,[
                'label' => 'label.min_amount_name',
                'required' => false,
                'attr' => [
                    'placeholder' => 'label.min_amount_name',
                ]
            ])
            ->add('mstMinimumContributionCurrency', EntityType::class, [
                'class' => MstCurrency::class,
//                'placeholder' => 'placeholder.form.select',
                'label' => 'label.min_currency_name',
                'required' => true,
                'choice_label' => 'iso3',
                'query_builder'=> function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->where("c.isActive = :isActive")
                        ->setParameter('isActive', 1);
                }
            ])
            ->add('isDistributedEvent', CheckboxType::class,[
                'label' => 'label.is_distributed_event',
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
            'data_class' => TrnCrowdFundEvent::class,
        ]);
    }
}
