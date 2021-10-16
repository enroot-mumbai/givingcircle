<?php

namespace App\Form\Transaction;

use App\Entity\Master\MstAreaInterest;
use App\Entity\Master\MstBankAccountType;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use App\Entity\Master\MstDaysOfWeek;
use App\Entity\Master\MstJoinBy;
use App\Entity\Master\MstState;
use App\Entity\Master\MstStatus;
use App\Entity\Transaction\TrnMaterialInKindCircleEventCollectionCentre;
use App\Entity\Transaction\TrnCollectionCentreDetails;
use App\Form\Master\MstDaysOfWeekType;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
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
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class TrnMaterialInKindCircleEventCollectionCentreType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $CollectionCentreDetails = function ($form, $data) {
            $formCollectionCentreDetailsOptions = [
                'label' => 'label.collection_centre',
                'class' => TrnCollectionCentreDetails::class,
                'required' => true,
                /*'query_builder' => function (EntityRepository $dr) use ($data) {
                    if (null === $data) {
                        $appuser_id = null;
                    } elseif (null != $data && is_array($data)) {
                        $appuser_id = $data["appUser"];
                    } else {
                        $appuser_id = $data->getAppUser()?$data->getAppUser()->getId():null;
                    }
                    if( !empty($appuser_id))
                        return $dr->createQueryBuilder('s')->andWhere('s.appUser = :appUser')->setParameter('appUser', $appuser_id);
                    else
                        return $dr->createQueryBuilder('s');

                },*/
            ];
            $form
                ->add('trnCollectionCentreDetails', EntityType::class,$formCollectionCentreDetailsOptions, [
                    'label' => 'label.collection_centre',
                ])
            ;
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($CollectionCentreDetails) {
            $form = $event->getForm();
            $data = $event->getData();
            $CollectionCentreDetails ($form, $data);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($CollectionCentreDetails) {
            $form = $event->getForm();
            $data = $event->getData();
            $CollectionCentreDetails($form, $data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnMaterialInKindCircleEventCollectionCentre::class,
        ]);
    }
}
