<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\Libraries;

class JavaScriptRendererStrategy implements JavaScriptRendererStrategyInterface
{
    protected const ASSETS_PATH_JS = 'Assets/js';

    /**
     * @var array|null
     */
    private $javascripts;
    /**
     * @var \ACP3\Core\Assets
     */
    private $assets;
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    private $fileResolver;
    /**
     * @var \ACP3\Core\Assets\Libraries
     */
    private $libraries;

    public function __construct(Assets $assets, Assets\FileResolver $fileResolver, Libraries $libraries)
    {
        $this->assets = $assets;
        $this->fileResolver = $fileResolver;
        $this->libraries = $libraries;
    }

    /**
     * Fetches the javascript files of all enabled frontend frameworks/libraries.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function fetchLibraries(): void
    {
        foreach ($this->libraries->getEnabledLibraries() as $library) {
            if (!$library->getJs()) {
                continue;
            }

            foreach ($library->getJs() as $javascript) {
                $this->javascripts[] = $this->fileResolver->getWebStaticAssetPath(
                    $library->getModuleName(),
                    static::ASSETS_PATH_JS,
                    $javascript
                );
            }
        }
    }

    /**
     * Fetches the theme javascript files.
     */
    protected function fetchThemeJavaScript(): void
    {
        foreach ($this->assets->fetchAdditionalThemeJsFiles() as $file) {
            $this->javascripts[] = $this->fileResolver->getWebStaticAssetPath('System', static::ASSETS_PATH_JS, $file);
        }

        // Include the general js file of the layout
        $this->javascripts[] = $this->fileResolver->getWebStaticAssetPath('System', static::ASSETS_PATH_JS, 'layout.js');
    }

    /**
     * {@inheritDoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(): string
    {
        if ($this->javascripts === null) {
            $this->initialize();
        }

        $currentTimestamp = time();

        return array_reduce($this->javascripts, static function ($accumulator, $javascript) use ($currentTimestamp) {
            if ($javascript === '') {
                return $accumulator;
            }

            return $accumulator . "<script defer src=\"{$javascript}?{$currentTimestamp}\"></script>\n";
        }, '');
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function initialize(): void
    {
        $this->assets->initializeTheme();

        $this->javascripts = [];

        $this->fetchLibraries();
        $this->fetchThemeJavaScript();
    }
}
