<?php

namespace App\Form\Master;

use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstState;
use App\Repository\Master\MstCityRepository;
use App\Repository\Master\MstStateRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class MstCityType extends AbstractType
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
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('mstCountry', EntityType::class, [
                'class' => MstCountry::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.country',
                'required' => true,
                'attr' => [
                    'class' => 'mstcountry'
                ]
            ])
            ->add('latitude', TextType::class,[
                'label' => 'label.latitude',
                'required' => false,
            ])
            ->add('longitude', TextType::class,[
                'label' => 'label.longitude',
                'required' => false,
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
            ];
            $form
                ->add('mstState', EntityType::class,$formStateOptions, [
                    'label' => 'label.state',
                ])
                ->add('city', TextType::class,[
                    'label' => 'label.city',
                    'required' => true,
                ]);
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
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => MstCity::class,
            'constraints' => [
                new Callback([$this, 'validateCity']),
            ],
        ]);
    }
    /**
     * @param object $data
     * @param ExecutionContextInterface $context
     */
    public function validateCity(object $data, ExecutionContextInterface $context): void
    {
        $checkValue = $this->mstCityRepository->findOneBy([
            'city' => $data->getCity(),
            'mstCountry' => $data->getCountryId(),
            'mstState' => $data->getStateId(),
        ]);
        if ($checkValue) {
            $context->buildViolation('The value is already defined in the system')
                ->atPath('city')
                ->addViolation();
        }
    }
}
