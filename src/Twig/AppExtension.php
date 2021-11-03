<?php
namespace App\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;


class AppExtension extends AbstractExtension
{
    public function getFilters()
    {
        return [
            new TwigFilter('strToInt', [$this, 'formatPrice']),
        ];
    }

    public function formatPrice($string)
    {
        $int = boolval($string);

        return $int;
    }
}