<?php

namespace App\Twig\SystemApp;

use App\Entity\SystemApp\AppModule;
use App\Repository\SystemApp\AppModuleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppModuleExtension extends AbstractExtension
{
    private $em;
    private $appModuleRepository;

    public function __construct(EntityManagerInterface $em, AppModuleRepository $appModuleRepository)
    {
        $this->em = $em;
        $this->appModuleRepository = $appModuleRepository;
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
            new TwigFunction('get_app_module', [$this, 'getAppModule']),
            new TwigFunction('get_app_module_name', [$this, 'getAppModuleName']),
        ];
    }

    public function getAppModule()
    {

        return $this->appModuleRepository->getAppModule();
    }

    public function getAppModuleName()
    {

        $path = explode("/", parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));
        $moduleValue = $path[2];

           return $this->em->getRepository(AppModule::class)->findOneBy(['moduleValue' => $moduleValue]);


    }
}
