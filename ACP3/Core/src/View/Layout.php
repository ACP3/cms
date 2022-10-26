<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\View;

use ACP3\Core\Environment\ThemePathInterface;
use Webmozart\Glob\Glob;

class Layout
{
    public function __construct(private readonly ThemePathInterface $themePath)
    {
    }

    /**
     * This method returns all available layouts of the current theme, except for the default layout file.
     *
     * @return string[]
     *
     * @throws \JsonException
     */
    public function getAvailableLayoutFiles(): array
    {
        $paths = [
            $this->themePath->getDesignPathInternal() . '/*/Resources/View/*/layout.tpl',
            $this->themePath->getDesignPathInternal() . '/*/Resources/View/*/layout.*.tpl',
            $this->themePath->getDesignPathInternal() . '/*/Resources/View/layout.tpl',
            $this->themePath->getDesignPathInternal() . '/*/Resources/View/layout.*.tpl',
        ];

        $layouts = [];
        foreach ($paths as $path) {
            $layouts = array_merge($layouts, Glob::glob($path));
        }
        $layouts = array_filter($layouts, fn ($layout) => $layout !== $this->themePath->getDesignPathInternal() . '/System/Resources/View/layout.tpl');

        return array_map(fn ($value) => str_replace([$this->themePath->getDesignPathInternal() . '/', '/Resources/View/'], ['', '/'], (string) $value), $layouts);
    }
}
