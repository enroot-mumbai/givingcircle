<?php

namespace App\Form\Form;

use App\Entity\Form\FormReport;
use App\Entity\Master\MstCity;
use App\Entity\Master\MstCountry;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class FormReportSelfType extends AbstractType
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reportFor', HiddenType::class,[
                'label' =>false,
                'data' => 'self',
                'empty_data' => 'self',
            ])
            ->add('firstName', TextType::class,[
                'label' =>false,
                'required' => true,
                'attr' => [
                    'maxlength' => 20
                ],
                'constraints' => [
                    new NotBlank(),
                    /*new Length([
                        'min' => 4,
                        'max' => 100,
                    ]),*/
                ]

            ])
            ->add('lastName', TextType::class,[
                'label' =>false,
                'required' => true,
                'attr' => [
                    'maxlength' => 20
                ],
                'constraints' => [
                    new NotBlank(),
                    /*new Length([
                        'min' => 3,
                        'max' => 100,
                    ]),*/
                ]

            ])
            ->add('emailAddress', EmailType::class,[
                'label' =>false,
                'required' => true,
                'attr' => [
                    'maxlength' => 50
                ],
                'constraints' => [
                    new NotBlank(),
                    /*new Length([
                        'min' => 10,
                        'max' => 50,
                    ]),*/
                ]
            ])
            ->add('mobileNumber', TelType::class,[
                'label' =>false,
                'required' => true,
                'attr' => [
                    'minlength' => '10',
                    'maxlength' => '10',
                    'class' => 'mob-no-input',
                ],
                'constraints' => [
                    new NotBlank(),
                    /*new Length([
                        'min' => 10,
                        'max' => 10,
                    ]),*/
                ]
            ])
            ->add('mstCity', EntityType::class,[
                'class' => MstCity::class,
                'label' =>false,
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    /*new Length([
                        'min' => 3,
                        'max' => 30,
                    ]),*/
                ],
                'placeholder' => '',
                'query_builder' => function (EntityRepository $dr) {
                    $objCountry = $this->em->getRepository(MstCountry::class)->findOneBy(['country' => 'India']);
                    return $dr->createQueryBuilder('c')->andWhere('c.mstCountry =:country')->setParameter('country', $objCountry->getId());
                },

            ])
            ->add('messageDetail', TextareaType::class,[
                'label' =>false,
                'required' => false,
                /*'constraints' => [
                    new NotBlank(),
                    new Length([
                        'max' => 1000,
                    ]),
                ]*/
            ])
            ->add('remarks', TextareaType::class,[
                'label' =>false,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FormReport::class,
        ]);
    }
}
