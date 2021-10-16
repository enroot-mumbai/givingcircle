<?php

/**
 * This file is part of Twig.
 *
 * (c) 2014-2019 Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Twig\Common;

use DateTime;
use Symfony\Component\Translation\IdentityTranslator;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * @author Robin van der Vleuten <robinvdvleuten@gmail.com>
 */
class DateExtension extends AbstractExtension
{
    public static $units = [
        'y' => 'year',
        'm' => 'month',
        'd' => 'day',
        'h' => 'hour',
        'i' => 'minute',
        's' => 'second',
    ];

    private $translator;
    private $env;

    public function __construct(Environment $env, TranslatorInterface $translator = null)
    {
        // Ignore the IdentityTranslator, otherwise the parameters won't be replaced properly
        if ($translator instanceof IdentityTranslator) {
            $translator = null;
        }

        $this->translator = $translator;
        $this->env = $env;
    }

    public function getFilters()
    {
        return [
            new TwigFilter('time_diff', [$this, 'diff']),

        ];
    }
    /**
     * @return array|TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('add_time', [$this, 'addTime']),
        ];
    }

    /**
     * Filters for converting dates to a time ago string like Facebook and Twitter has.
     *
     * @param string|DateTime $date a string or DateTime object to convert
     * @param string|DateTime $now  A string or DateTime object to compare with. If none given, the current time will be used.
     *
     * @return string the converted time
     */
    public function diff($date, $now = null)
    {
        // Convert both dates to DateTime instances.
        $date = twig_date_converter($this->env, $date);
        $now = twig_date_converter($this->env, $now);

        // Get the difference between the two DateTime objects.
        $diff = $date->diff($now);

        // Check for each interval if it appears in the $diff object.
        foreach (self::$units as $attribute => $unit) {
            $count = $diff->$attribute;

            if (0 !== $count) {
                return $this->getPluralizedInterval($count, $diff->invert, $unit);
            }
        }

        return '';
    }

    private function getPluralizedInterval($count, $invert, $unit)
    {
        if ($this->translator) {
            $id = sprintf('diff.%s.%s', $invert ? 'in' : 'ago', $unit);

            return $this->translator->transChoice($id, $count, ['%count%' => $count], 'date');
        }

        if (1 !== $count) {
            $unit .= 's';
        }

        return $invert ? "in $count $unit" : "$count $unit ago";
    }

    /**
     * @param $date
     * @param $nHour
     * @return false|string
     */
    public function addTime($date, $nHour) {
        return date('Y-m-d H:i',strtotime("+$nHour hour",strtotime($date)));
    }
}
