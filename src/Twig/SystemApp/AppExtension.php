<?php

namespace App\Twig\SystemApp;

use App\Service\FileUploaderHelper;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension implements ServiceSubscriberInterface
{
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
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
            new TwigFunction('uploaded_file', [$this, 'getUploadedFilePath']),
        ];
    }

    public function getUploadedFilePath(string $path): string
    {
        return $this->container->get(FileUploaderHelper::class)->getPublicPath($path);
        // ...
    }

    public static function getSubscribedServices()
    {
        return [
            FileUploaderHelper::class
        ];

    }
}
