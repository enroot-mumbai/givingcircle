<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsFaq;
use App\Entity\Cms\CmsNotification;
use App\Entity\Organization\OrgCompany;
use App\Entity\Product\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsNotificationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('notificationName', TextType::class,[
                'label' => 'label.notification_name',
                'attr' => [
                    'class' => '',
                ]
            ])
            ->add('emailSubject', TextareaType::class,[
                'label' => 'label.email_subject',
                'required' => false,
                'attr' => [
                    'class' => '',
                ]
            ])
            ->add('whatappMsg', TextareaType::class,[
                'label' => 'label.whats_app_message',
                'required' => false,
                'attr' => [
                    'class' => 'textarea',
                ]
            ])
            ->add('textMessage', TextareaType::class,[
                'label' => 'label.text_message',
                'required' => false,
                'attr' => [
                    'class' => '',
                ]
            ])
            ->add('pushNotification', TextareaType::class,[
                'label' => 'label.push_notification',
                'required' => false,
                'attr' => [
                    'class' => '',
                ]
            ])
            ->add('systemNotification', TextareaType::class,[
                'label' => 'label.system_notification',
                'required' => false,
                'attr' => [
                    'class' => '',
                ]
            ])
            ->add('email', TextareaType::class,[
                'label' => 'label.email_content',
                'required' => false,
                'attr' => [
                    'class' => ' textarea',
                ]
            ])

            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsNotification::class,
        ]);
    }
}
