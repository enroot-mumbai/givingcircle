<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsMedia;
use App\Entity\Cms\CmsSocialPost;
use App\Entity\Master\MstMediaCategory;
use App\Entity\Master\MstMediaSubCategory;
use App\Entity\Master\MstSocial;
use App\Entity\Organization\OrgCompany;
use App\Service\CommonHelper;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;

class CmsSocialPostEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('postLink', TextType::class,[
                'label' => 'label.link',
                'required' => false,
                'attr' => [
                    'readonly' => 'readonly'
                ]
            ])
            ->add('postMessage', TextareaType::class,[
                'label' => 'label.message',
                'required' => true
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsSocialPost::class,
        ]);
    }
}
