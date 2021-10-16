<?php

namespace App\Form\Cms;

use App\Entity\Cms\CmsPage;
use App\Entity\Master\MstProductCategory;
use App\Entity\Master\MstProductSubCategory;
use App\Entity\Organization\OrgCompany;
use App\Form\Seo\SeoContentType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CmsPageType extends AbstractType
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('orgCompany', EntityType::class,[
                'label' => 'label.company',
                'class' => OrgCompany::class,
                'required' => 'true'
            ])
            ->add('pageName', TextType::class,[
                'label' => 'label.name',
                'required' => true,
            ])
            ->add('pageTitle', TextType::class,[
                'label' => 'label.title',
                'required' => true,
            ])
            ->add('slugName', TextType::class,[
                'label' => 'label.slug_name',
                'required' => true,
            ])
            ->add('cmsPageContent', CollectionType::class,[
                'label'=>false,
                'entry_type' => CmsPageContentType::class,
                'entry_options' => [
                    'label' => false,
                ],
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference'=>false,

            ])
            ->add('pageRoute', TextType::class,[
                'label' => 'Page Routing',
                'required' => false,
                'help' => 'Be careful while edit / updating this field, as it will effect the page url',
            ])
            ->add('parentId', ChoiceType::class,[
                'label' => 'label.parent_page',
                'choices' => $this->em->getRepository(CmsPage::class)->getParentPage(),
                'required' => false,
                'placeholder' => 'Parent Page (If applicable)',
            ])
            ->add('cmsIntroEmail', EmailType::class,[
                'required' => false,
                'label' => 'label.email',
            ])
            ->add('displaySequence', NumberType::class,[
                'required' => false,
                'label' => 'label.display_sequence',
            ])
            /*
            ->add('mstProductCategory', EntityType::class,[
                'label' => 'label.product_category',
                'class' => MstProductCategory::class,
                'help' => 'Select if you want to link this page to a specific product category',
                'required' => false,
                'placeholder' => 'Product Category (If applicable)',
            ])*/
            ->add('isActive', CheckboxType::class,[
                'required'=> false,
                'label' => 'label.is_active',
            ])
            ->add('seoContent', SeoContentType::class,[
                'label' => false,
            ])
        ;
        /**
         * @param $form
         * @param $data
         */
        $refreshEvent = function ($form, $data) {
            $formOptions = [
                'label' => 'label.product_subcategory',
                'class' => MstProductSubCategory::class,
                'choice_label' => 'indentedName',
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($data) {
                    if( array_key_exists('mstProductCategory', $data) )
                    {
                        $category_id = $data["mstProductCategory"];
                    }else{
                        $category_id = $data->getMstProductCategory()?$data->getMstProductCategory():null;
                    }
                    return $er->createQueryBuilder('sc')->leftJoin('sc.mstProductCategory', 'c')->andWhere('c.id =:category')->setParameter('category',$category_id);
                },
            ];
            /*
            $form
                ->add('mstProductSubCategory', EntityType::class,$formOptions, [
                    'label' => 'label.product_subcategory',
                ]);*/
        };
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($refreshEvent) {
            $form = $event->getForm();
            $data = $event->getData();
            $refreshEvent ($form, $data);
        });
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($refreshEvent) {
            $form = $event->getForm();
            $data = $event->getData();
            if (array_key_exists('mstProductCategory', $data)) {
                $refreshEvent($form, $data);
            }
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CmsPage::class,
        ]);
    }
}
