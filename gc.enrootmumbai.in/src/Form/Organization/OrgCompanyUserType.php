<?php

namespace App\Form\Organization;

use App\Entity\SystemApp\AppRole;
use App\Entity\SystemApp\AppUser;
use App\Repository\Organization\OrgCompanyOfficeRepository;
use App\Repository\SystemApp\AppRoleRepository;
use App\Repository\SystemApp\AppUserCategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrgCompanyUserType extends AbstractType
{
    /**
     * @var OrgCompanyOfficeRepository
     */
    private $orgCompanyOffice;
    /**
     * @var AppRoleRepository
     */
    private $appRolesRepository;
    /**
     * @var AppUserCategoryRepository
     */
    private $appUserCategoryRepository;
    /**
     * OrgCompanyUserType constructor.
     * @param OrgCompanyOfficeRepository $orgCompanyOfficeRepository
     * @param AppRoleRepository $appRolesRepository
     * @param AppUserCategoryRepository $appUserCategoryRepository
     */
    public function __construct(OrgCompanyOfficeRepository $orgCompanyOfficeRepository, AppRoleRepository $appRolesRepository, AppUserCategoryRepository $appUserCategoryRepository)
    {
        $this->orgCompanyOffice = $orgCompanyOfficeRepository;
        $this->appRolesRepository = $appRolesRepository;
        $this->appUserCategoryRepository = $appUserCategoryRepository;
    }
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('userName', TextType::class,[
                'label' => 'label.username',
                'required' => true,
            ])
            ->add('appUserInfo', OrgCompanyAppUserInfoType::class,[
                'label' => false,
                'data' => $options['data']->getappUserInfo(),
            ])
            ->add('appUserCategory', ChoiceType::class,[
                'label' => 'label.usercategory',
                'choices'       => $this->appUserCategoryRepository->getUserCategory(),
                'choice_value' => 'id',
                'choice_label' => 'userCategory',
                'required' => true
            ])
            ->add('userRole', ChoiceType::class,[
                'choices'       => $this->appRolesRepository->getLimitedRoles(),
                'required'      => true,
                'multiple'      => true,
                'expanded'      => true,
                'label'         => 'label.role',
            ])
            ->add('isActive', CheckboxType::class,[
                'label' => 'label.is_active',
                'required' => false,
                'attr' => [
                    'checked' => 'checked'
                ]
            ]);
    }
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AppUser::class,
        ]);
    }
}
