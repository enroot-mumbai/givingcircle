<?php

namespace App\Form\User;

use App\Entity\SystemApp\AppUserInfo;
use App\Form\SystemApp\AppUserInfoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class UserProfileType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userEmail', EmailType::class,[
                'label' => 'label.email',
            ])
            ->add('userFirstName', TextType::class,[
                'label' => 'label.firstName',
                'required' => true,
            ])
            ->add('userMiddleName', TextType::class,[
                'label' => 'label.middleName',
                'required' => false,
            ])
            ->add('userLastName', TextType::class,[
                'label' => 'label.lastName',
                'required' => true,
            ])
            ->add('userAvatarImage', FileType::class,[
                'required' => false,
                'mapped' => false,
                'label' => 'label.uploadImage',
                'constraints' => [
                    new File([
                            'maxSize' => '1000k',
                            'maxSizeMessage' => 'The maximum file upload size is 1000 kb.',
                            'mimeTypes' => [
                                'image/jpg',
                                'image/png',
                                'image/jpeg'
                            ],
                            'mimeTypesMessage' => 'Please upload a valid jpg or png file.',
                        ]
                    )
                ],
                'attr' => [
                    'class' => 'col-sm-5',
                    'placeholder' => 'Upload a file..',
                ]
            ])
        ;
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppUserInfo::class,
        ]);
    }
}
