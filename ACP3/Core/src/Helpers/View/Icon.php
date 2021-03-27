<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View;

use ACP3\Core\Assets\FileResolver;

class Icon
{
    /**
     * @var FileResolver
     */
    private $fileResolver;

    public function __construct(FileResolver $fileResolver)
    {
        $this->fileResolver = $fileResolver;
    }

    public function __invoke(string $iconSet, string $icon, array $options = []): string
    {
        $path = $this->fileResolver->getWebStaticAssetPath('system', 'Assets/sprites', $iconSet . '.svg');

        if (\array_key_exists('pathOnly', $options) && $options['pathOnly'] === true) {
            return $path . '#' . $icon;
        }

        $additionalCssSelectors = '';
        if (\array_key_exists('cssSelectors', $options)) {
            $additionalCssSelectors = ' ' . $options['cssSelectors'];
        }

        $title = '';
        if (\array_key_exists('title', $options)) {
            $title = "<title>{$options['title']}</title>";
        }

        return <<<HTML
<svg class="svg-icon svg-icon__{$icon}{$additionalCssSelectors}" fill="currentColor">{$title}<use xlink:href="$path#$icon"></use></svg>
HTML;
    }
}
