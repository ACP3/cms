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
     * @var string[]
     */
    private array $javascripts = [];

    public function __construct(private readonly Assets $assets, private readonly Assets\FileResolver $fileResolver, private readonly Libraries $libraries)
    {
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
                $this->addFiles(
                    $this->fileResolver->getWebStaticAssetPath(
                        $library->getModuleName(),
                        static::ASSETS_PATH_JS,
                        $javascript
                    )
                );
            }
        }
    }

    public function addFiles(array $files): void
    {
        foreach ($files as $file) {
            if (!empty($file) && !\in_array($file, $this->javascripts, true)) {
                $this->javascripts[] = $file;
            }
        }
    }

    /**
     * Fetches the theme javascript files.
     */
    protected function fetchThemeJavaScript(): void
    {
        foreach ($this->assets->fetchAdditionalThemeJsFiles() as $file) {
            $this->addFiles(
                $this->fileResolver->getWebStaticAssetPath(
                    'System', static::ASSETS_PATH_JS, $file
                ),
            );
        }

        // Include the general js file of the layout
        $this->addFiles(
            $this->fileResolver->getWebStaticAssetPath(
                'System', static::ASSETS_PATH_JS, 'layout.js'
            ),
        );
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(): string
    {
        $this->initialize();

        return array_reduce(
            $this->javascripts,
            static fn ($accumulator, $javascript) => $accumulator . "<script defer src=\"{$javascript}\"></script>\n",
            ''
        );
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function initialize(): void
    {
        $this->assets->initializeTheme();

        $backup = $this->javascripts;

        $this->javascripts = [];

        $this->fetchLibraries();
        $this->fetchThemeJavaScript();

        $this->addFiles($backup);
    }
}
