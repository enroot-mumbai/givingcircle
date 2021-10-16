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
use App\Entity\Transaction\TrnVolunterCircleEventSubEvents;
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

class TrnVolunterCircleEventSubEventsType extends AbstractType
{
    public function __construct()
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subEventName', TextType::class,[
                'label' => 'label.subevent_name',
                'required' => true
            ])
            ->add('numberOfHours', IntegerType::class,[
                'label' => 'label.number_of_hours',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TrnVolunterCircleEventSubEvents::class,
        ]);
    }
}
