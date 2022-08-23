<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers\View;

use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Helpers\View\Exception\SvgIconNotFoundException;

class Icon
{
    public function __construct(private readonly FileResolver $fileResolver)
    {
    }

    /**
     * @param array{cssSelectors?: string, title?: string} $options
     */
    public function __invoke(string $iconSet, string $icon, array $options = []): string
    {
        $additionalCssSelectors = '';
        if (\array_key_exists('cssSelectors', $options)) {
            $additionalCssSelectors = ' ' . $options['cssSelectors'];
        }

        $title = '';
        if (\array_key_exists('title', $options)) {
            $title = "<title>{$options['title']}</title>";
        }

        $path = $this->fileResolver->getStaticAssetPath('system', 'Assets/svgs/' . $iconSet, $icon . '.svg');

        if ($path === '') {
            throw new SvgIconNotFoundException(sprintf('Could not find SVG icon "%s" from iconset "%s"', $icon, $iconSet));
        }

        return str_replace(
            ['<svg', '<path'],
            ["<svg class=\"svg-icon svg-icon__{$icon}{$additionalCssSelectors}\" fill=\"currentColor\"", "{$title}<path"],
            file_get_contents($path)
        );
    }
}
