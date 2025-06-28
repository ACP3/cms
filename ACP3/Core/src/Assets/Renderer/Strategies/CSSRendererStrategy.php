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
     * @var string[]
     */
    private array $stylesheets = [];

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
                $this->addFiles(
                    $this->fileResolver->getWebStaticAssetPath(
                        $library->getModuleName(),
                        static::ASSETS_PATH_CSS,
                        $stylesheet
                    ),
                );
            }
        }
    }

    public function addFiles(array $files): void
    {
        foreach ($files as $file) {
            if (!empty($file) && !\in_array($file, $this->stylesheets, true)) {
                $this->stylesheets[] = $file;
            }
        }
    }

    /**
     * Fetches the theme stylesheets.
     */
    private function fetchThemeStylesheets(): void
    {
        foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
            $this->addFiles(
                $this->fileResolver->getWebStaticAssetPath(
                    'System',
                    static::ASSETS_PATH_CSS,
                    trim($file)
                ),
            );
        }

        $this->addFiles(
            $this->fileResolver->getWebStaticAssetPath(
                'System',
                static::ASSETS_PATH_CSS,
                'layout.css'
            ),
        );
    }

    /**
     * Fetches the stylesheets of all currently enabled modules.
     */
    private function fetchModuleStylesheets(): void
    {
        $area = $this->request->getArea();

        foreach ($this->modules->getInstalledModules() as $module) {
            $this->addFiles(
                $this->fileResolver->getWebStaticAssetPath(
                    $module['name'],
                    static::ASSETS_PATH_CSS,
                    'style.css'
                ),
            );

            if ($area === AreaEnum::AREA_ADMIN) {
                $this->addFiles(
                    $this->fileResolver->getWebStaticAssetPath(
                        $module['name'],
                        static::ASSETS_PATH_CSS,
                        'admin.css'
                    ),
                );
            }

            // Append custom styles to the default module styling
            $this->addFiles(
                $this->fileResolver->getWebStaticAssetPath(
                    $module['name'],
                    static::ASSETS_PATH_CSS,
                    'append.css'
                ),
            );
        }
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function renderHtmlElement(): string
    {
        $this->initialize();

        return array_reduce(
            array_filter($this->stylesheets, static fn ($stylesheet) => !empty($stylesheet)),
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

        $backup = $this->stylesheets;
        $this->stylesheets = [];

        // The sort order is important here, as module should be allowed to override the styles a library provides.
        // Also, themes should be allowed to override the styles modules defined.
        $this->fetchLibraries();
        $this->fetchModuleStylesheets();
        $this->fetchThemeStylesheets();

        $this->addFiles($backup);
    }
}
