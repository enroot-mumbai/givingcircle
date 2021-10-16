<?php


namespace App\Twig\Cms;

use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class CmsExtension  extends AbstractExtension
{
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_change_makers', [$this, 'getChangeMakers']),
            ];
    }

    public function getChangeMakers()
    {
        return $this->em->getRepository(CmsClient::class)->findBy(['showOnHomepage' => 1]);
    }

}
