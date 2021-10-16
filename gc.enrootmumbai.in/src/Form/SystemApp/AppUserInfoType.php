<?php

namespace App\Form\SystemApp;

use App\Entity\SystemApp\AppUserInfo;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AppUserInfoType extends AbstractType
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
       $builder
            ->add('userEmail', EmailType::class,[
                'label' => 'label.email',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('userFirstName', TextType::class,[
                'label' => 'label.firstName',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('userMiddleName', TextType::class,[
                'label' => 'label.middleName',
                'required' => false,
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
            ->add('userLastName', TextType::class,[
                'label' => 'label.lastName',
                'attr' => [
                    'class' => 'col-sm-3'
                ]
            ])
        ;

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppUserInfo::class,
        ]);
    }
}
