<?php

namespace App\Form\Transaction;

use App\Entity\Transaction\TrnOrganizationDetails;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
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

class TrnOrganizationUploadType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('trnOrganizationUploadDocuments', CollectionType::class, [
                'entry_type' => TrnOrganizationUploadDocumentsType::class,
                'required' => false,
                'label' => false,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,
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
