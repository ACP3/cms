<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Assets\Libraries;
use ACP3\Core\Modules;

class CSSRendererStrategy implements CSSRendererStrategyInterface
{
    protected const ASSETS_PATH_CSS = 'Assets/css';

    /**
     * @var array|null
     */
    private $stylesheets;
    /**
     * @var \ACP3\Core\Assets
     */
    private $assets;
    /**
     * @var \ACP3\Core\Assets\Libraries
     */
    private $libraries;
    /**
     * @var \ACP3\Core\Modules
     */
    private $modules;
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    private $fileResolver;

    public function __construct(Assets $assets, Libraries $libraries, Modules $modules, FileResolver $fileResolver)
    {
        $this->assets = $assets;
        $this->libraries = $libraries;
        $this->modules = $modules;
        $this->fileResolver = $fileResolver;
    }

    /**
     * Fetch all stylesheets of the enabled frontend frameworks/libraries.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function fetchLibraries(): void
    {
        foreach ($this->libraries->getEnabledLibraries() as $library) {
            if (!$library->getCss() || $library->isDeferrableCss()) {
                continue;
            }

            foreach ($library->getCss() as $stylesheet) {
                $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
                    $library->getModuleName(),
                    static::ASSETS_PATH_CSS,
                    $stylesheet
                );
            }
        }
    }

    /**
     * Fetches the theme stylesheets.
     */
    private function fetchThemeStylesheets(string $layout): void
    {
        foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
            $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
                '',
                static::ASSETS_PATH_CSS,
                trim($file)
            );
        }

        $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
            '',
            static::ASSETS_PATH_CSS,
            $layout . '.css'
        );
    }

    /**
     * Fetches the stylesheets of all currently enabled modules.
     */
    private function fetchModuleStylesheets(): void
    {
        foreach ($this->modules->getInstalledModules() as $module) {
            $stylesheet = $this->fileResolver->getWebStaticAssetPath(
                $module['name'],
                static::ASSETS_PATH_CSS,
                'style.css'
            );
            if ('' !== $stylesheet) {
                $this->stylesheets[] = $stylesheet;
            }

            // Append custom styles to the default module styling
            $appendStylesheet = $this->fileResolver->getWebStaticAssetPath(
                $module['name'],
                static::ASSETS_PATH_CSS,
                'append.css'
            );
            if ('' !== $appendStylesheet) {
                $this->stylesheets[] = $appendStylesheet;
            }
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(string $layout = 'layout'): string
    {
        if ($this->stylesheets === null) {
            $this->initialize($layout);
        }

        $currentTimestamp = time();

        return array_reduce($this->stylesheets, static function ($accumulator, $stylesheet) use ($currentTimestamp) {
            return $accumulator . '<link rel="stylesheet" type="text/css" href="' . $stylesheet . '?' . $currentTimestamp . '">' . "\n";
        }, '');
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function initialize(string $layout): void
    {
        $this->assets->initializeTheme();

        $this->stylesheets = [];

        $this->fetchLibraries();
        $this->fetchModuleStylesheets();
        $this->fetchThemeStylesheets($layout);
    }
}
