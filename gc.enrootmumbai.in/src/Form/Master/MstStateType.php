<?php

namespace App\Form\Master;

use App\Entity\Master\MstState;
use App\Entity\Master\MstCountry;
use App\Repository\Master\MstStateRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MstStateType extends AbstractType
{
    /**
     * @var MstStateRepository
     */
    private $mstStateRepository;
    /**
     * MstStateType constructor.
     * @param MstStateRepository $mstStateRepository
     */
    public function __construct(MstStateRepository $mstStateRepository)
    {
        $this->mstStateRepository = $mstStateRepository;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', TextType::class,[
                'label' => 'label.state',
            ])
            ->add('fipsCode', TextType::class,[
                'label' => 'label.fips_code',
                'attr' => [
                    'maxlength' => '5',
                    'minlength' => '2'
                ]
            ])
            ->add('iso2', TextType::class,[
                'label' => 'label.iso2',
                'attr' => [
                    'maxlength' => '2',
                    'minlength' => '2'
                ]
            ])
            ->add('mstCountry', EntityType::class,[
                'class' => MstCountry::class,
                'label' => 'label.country',
            ])
        ;
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MstState::class,
            'constraints' => [
                new Callback([$this, 'validateState']),
            ],
        ]);
    }
    public function validateState(object $data, ExecutionContextInterface $context): void
    {
        $checkValue = $this->mstStateRepository->findOneBy([
            'state' => $data->getState(),
            'mstCountry' => $data->getMstCountry()->getId(),
        ]);
        if ($checkValue) {
            $context->buildViolation('The value is already defined in the system')
                ->atPath('state')
                ->addViolation();
        }
    }
}
