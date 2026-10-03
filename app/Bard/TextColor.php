<?php

namespace App\Bard;

use Tiptap\Core\Mark;
use Tiptap\Utils\HTML;

/**
 * Bard mark for brand-colored text. Stored as {type: textColor, attrs: {color: primary|highlight}}
 * and rendered as <span class="text-color-{color}">, styled in site.css and cp.css.
 */
class TextColor extends Mark
{
    public const COLORS = ['primary', 'highlight'];

    public static $name = 'textColor';

    public function addAttributes()
    {
        return [
            'color' => [
                'parseHTML' => function ($DOMNode) {
                    foreach (self::COLORS as $color) {
                        if (str_contains($DOMNode->getAttribute('class'), "text-color-{$color}")) {
                            return $color;
                        }
                    }

                    return null;
                },
                'renderHTML' => fn ($attributes) => in_array($attributes->color ?? null, self::COLORS, true)
                    ? ['class' => "text-color-{$attributes->color}"]
                    : null,
            ],
        ];
    }

    public function parseHTML()
    {
        return [
            ['tag' => 'span.text-color-primary'],
            ['tag' => 'span.text-color-highlight'],
        ];
    }

    public function renderHTML($mark, $HTMLAttributes = [])
    {
        return ['span', HTML::mergeAttributes($HTMLAttributes), 0];
    }
}
