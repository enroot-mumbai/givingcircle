<?php


namespace App\Twig\Common;

use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;


class TextExtension extends AbstractExtension
{
    private $env;
    public function __construct(Environment $env)
    {
        $this->env = $env;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('truncate', [$this, 'twig_truncate_filter']),
        ];
    }

    function twig_truncate_filter($value, $length = 100, $preserve = false, $separator = '...')
    {
        if (mb_strlen($value, $this->env->getCharset()) > $length) {
            if ($preserve) {
                // If breakpoint is on the last word, return the value without separator.
                if (false === ($breakpoint = mb_strpos($value, ' ', $length, $this->env->getCharset()))) {
                    return $value;
                }
                $length = $breakpoint;
            }
            return rtrim(mb_substr($value, 0, $length, $this->env->getCharset())).$separator;
        }
        return $value;
    }

}