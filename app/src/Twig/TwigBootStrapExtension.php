<?php
declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class TwigBootStrapExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('badge', [$this, 'badgeFilter'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Creates badges using the defined 'msg' argument.
     * Options can be 'color', 'geometric shape'
     */
    public function badgeFilter(string $msg, array $options = []): string
    {
        $options = array_merge([
            'color' => 'primary',
            'rounded' => false,
        ], $options);
        $color = $options['color'];
        $shape = $options['rounded'] ? 'badge-pill': '';

        $template = "<span class='badge badge-%s %s'>%s</span>";

        return sprintf($template, $color, $shape, $msg);
    }
}