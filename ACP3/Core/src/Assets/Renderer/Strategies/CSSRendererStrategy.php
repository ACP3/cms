<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Assets\Libraries;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Modules;

class CSSRendererStrategy implements CSSRendererStrategyInterface
{
    protected const ASSETS_PATH_CSS = 'Assets/css';

    /**
     * @var string[]|null
     */
    private ?array $stylesheets = null;

    public function __construct(
        private readonly RequestInterface $request,
        private readonly Assets $assets,
        private readonly Libraries $libraries,
        private readonly Modules $modules,
        private readonly FileResolver $fileResolver)
    {
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
    private function fetchThemeStylesheets(): void
    {
        foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
            $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
                'System',
                static::ASSETS_PATH_CSS,
                trim($file)
            );
        }

        $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
            'System',
            static::ASSETS_PATH_CSS,
            'layout.css'
        );
    }

    /**
     * Fetches the stylesheets of all currently enabled modules.
     */
    private function fetchModuleStylesheets(): void
    {
        $area = $this->request->getArea();

        foreach ($this->modules->getInstalledModules() as $module) {
            $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
                $module['name'],
                static::ASSETS_PATH_CSS,
                'style.css'
            );

            if ($area === AreaEnum::AREA_ADMIN) {
                $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
                    $module['name'],
                    static::ASSETS_PATH_CSS,
                    'admin.css'
                );
            }

            // Append custom styles to the default module styling
            $this->stylesheets[] = $this->fileResolver->getWebStaticAssetPath(
                $module['name'],
                static::ASSETS_PATH_CSS,
                'append.css'
            );
        }
    }

    /**
     * {@inheritDoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(): string
    {
        if ($this->stylesheets === null) {
            $this->initialize();
        }

        return array_reduce(
            array_filter($this->stylesheets, static fn ($stylesheet) => $stylesheet !== ''),
            static fn ($accumulator, $stylesheet) => $accumulator . '<link rel="stylesheet" type="text/css" href="' . $stylesheet . '">' . "\n",
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

        $this->stylesheets = [];

        // The sort order is important here, as module should be allowed to override the styles a library provides.
        // Also, themes should be allowed to override the styles modules defined.
        $this->fetchLibraries();
        $this->fetchModuleStylesheets();
        $this->fetchThemeStylesheets();
    }
}
