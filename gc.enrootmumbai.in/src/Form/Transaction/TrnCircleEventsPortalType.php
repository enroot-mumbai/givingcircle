<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstEventProductType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Entity\Transaction\TrnCircle;
use App\Entity\Transaction\TrnCircleEvents;
use App\Form\Master\MstDaysOfWeekType;
use App\Repository\Transaction\TrnCircleRepository;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
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
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Security;

class TrnCircleEventsPortalType extends AbstractType
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
     * @var Security
     */
    private $security;

    /**
     * @var MstStatus
     */
    private $objMstStatus;

    /**
     * @var TrnCircleRepository
     */
    private $trnCircleRepository;

    /**
     * MstCityType constructor.
     * @param MstStateRepository $mstStateRepository
     * @param MstCityRepository $mstCityRepository
     * @param Security $security
     * @param TrnCircleRepository $trnCircleRepository
     */
    public function __construct(MstStateRepository $mstStateRepository, MstCityRepository $mstCityRepository,
                                Security $security, TrnCircleRepository $trnCircleRepository)
    {
        $this->mstStateRepository = $mstStateRepository;
        $this->mstCityRepository = $mstCityRepository;
        $this->security = $security;
        $this->trnCircleRepository = $trnCircleRepository;
    }



    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->objMstStatus = $options['mstStatus'];
        $builder
            ->add('isCrowdFunding', CheckboxType::class,[
                'required' => true,
                'label' => 'label.is_crowd_funding',
                'attr' => [
                    'style' => 'display:none;'
                ]
            ])
            ->add('trnCircle', EntityType::class,[
                'class' => TrnCircle::class,
                'placeholder' => 'label.select_project',
                'label' => 'label.circle',
                'required' => true,
                'choices' => $this->trnCircleRepository->getAppUserProjects($this->security->getUser(), $this->objMstStatus)
            ])
            ->add('name', TextType::class,[
                'label' => 'label.circle_child_event',
                'required' => true,
                'attr' => [
                    'placeholder' => 'label.event_name_place_holder',
                ]

            ])
            ->add('eventPurpose', TextareaType::class,[
                'label' => 'label.event_purpose',
                'required' => true,
                'attr' => [
                    'class' => 'longtextarea',
//                    'placeholder' => 'label.event_goal_place_holder'
                ]
            ])
            ->add('mstJoinBy', EntityType::class, [
                'class' => MstJoinBy::class,
                'placeholder' => 'placeholder.form.select',
                'label' => 'label.circle_event_type_of_event',
                'required' => true,
                'attr' => [
                    'style' => 'display:none;'
                ]
            ])
            ->add('mstEventProductType', EntityType::class,[
                'label' => 'label.circle_event_product_type',
                'class' => mstEventProductType::class,
                'placeholder' => 'placeholder.form.select',
                'multiple'=> true,
                'required' => true,
                'attr' => [
                    'style' => 'display:none;'
                ]
            ])
            ->add('highlightsOfEvent', TextareaType::class,[
                'label' => 'label.highlights_of_event',
                'required' => true,
                'attr' => [
                    'class' => 'longtextarea',
//                    'placeholder' => 'label.event_highlights_place_holder'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnCircleEvents::class,
            'mstStatus' => null
        ]);

        // you can also define the allowed types, allowed values and
        // any other feature supported by the OptionsResolver component
        $resolver->setAllowedTypes('mstStatus', null);
    }
}