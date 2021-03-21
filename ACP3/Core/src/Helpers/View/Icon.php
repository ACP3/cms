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

    public function __invoke(string $iconSet, string $icon, ?string $additionalCssSelectors = null, ?string $title = null): string
    {
        $path = $this->fileResolver->getWebStaticAssetPath('system', 'Assets/sprites', $iconSet . '.svg');

        if ($additionalCssSelectors !== null) {
            $additionalCssSelectors = ' ' . $additionalCssSelectors;
        }

        if ($title !== null) {
            $title = "<title>$title</title>";
        }

        return <<<HTML
<svg class="svg-icon svg-icon__{$icon}{$additionalCssSelectors}" fill="currentColor">{$title}<use xlink:href="$path#$icon"></use></svg>
HTML;
    }
}
