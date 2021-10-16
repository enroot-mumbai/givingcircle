<?php

namespace App\Twig\SystemApp;

use App\Repository\SystemApp\AppUserRepository;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppUserExtension extends AbstractExtension
{
    private $userRepository;

    public function __construct(AppUserRepository $appUserRepository)
    {
        $this->userRepository = $appUserRepository;
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




}
