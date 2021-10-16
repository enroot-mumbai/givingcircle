<?php

namespace App\Twig\SystemApp;

use App\Entity\SystemApp\AppSubModule;
use App\Repository\SystemApp\AppSubModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppSubModuleExtension extends AbstractExtension
{

    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }


    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/2.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [$this, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_app_submodules', [$this, 'getAppSubModule']),
            new TwigFunction('get_app_submodule_name', [$this, 'getAppSubModuleName']),
            new TwigFunction('get_child_submodule_name', [$this, 'getChildSubModule']),
            new TwigFunction('get_submodule_by_id', [$this, 'getSubModuleById'])
        ];
    }

    public function getAppSubModule($module_id)
    {
 //       $path = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
 //       $moduleValue = $path[1];

        return $this->em->getRepository(AppSubModule::class)->getSubModule($module_id);
    }

    public function getAppSubModuleName()
    {
        $path = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        if ($path[2] == '') {
            $subModuleValue = $path[1];
        }
        else {
            $subModuleValue = $path[2];

        }

             return $this->em->getRepository(AppSubModule::class)->findOneBy(['subModuleValue' => $subModuleValue]);
    }

    public function getSubModuleById($id)
    {
        return $this->em->getRepository(AppSubModule::class)->find($id);

    }

    public function getChildSubModule($parentId, $isChild)
    {
        // Need to make changes to child submodules show that they are only access by user type id

        return $this->em->getRepository(AppSubModule::class)->getChildModule($parentId, $isChild);
    }
}
