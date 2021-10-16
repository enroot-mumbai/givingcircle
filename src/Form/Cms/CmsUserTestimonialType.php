<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsUserTestimonial;
use App\Entity\Organization\OrgCompany;
use App\Entity\SystemApp\AppUser;
use App\Repository\SystemApp\AppUserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsUserTestimonialType extends AbstractType
{
    private $appuser;
    public function __construct(AppUserRepository $appuser)
    {
        $this->appuser = $appuser;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('testimonialFor', TextType::class,[
                'label' => 'Testimonial For',
//                'class' => OrgCompany::class,
//                'placeholder' => 'placeholder.form.select',
                'required' => true
            ])
            ->add('testimonialDetail', TextareaType::class,[
                'label' => 'Testimonial',
//                'class' => OrgCompany::class,
//                'placeholder' => 'placeholder.form.select',
                'required' => true
            ])
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'Activate for portal',
                'help' => 'If selected, then the above testimonial will be shown on the portal',
                /*'attr' => [
                    'checked' => 'checked'
                ]*/
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsUserTestimonial::class,
        ]);
    }
}
