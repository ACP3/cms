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
    protected array $stylesheets = [];

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
    protected function fetchLibraries(): void
    {
        foreach ($this->libraries->getEnabledLibraries() as $library) {
            foreach ($library->getCss() as $stylesheet) {
                $this->addFiles(
                    $this->fileResolver->getStaticAssetPath(
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
            if (!empty($file) && preg_match('=\.css(\?.+)?$=i', $file) && !\in_array($file, $this->stylesheets, true)) {
                $this->stylesheets[] = $file;
            }
        }
    }

    /**
     * Fetches the theme stylesheets.
     */
    protected function fetchThemeStylesheets(): void
    {
        foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
            $this->addFiles(
                $this->fileResolver->getStaticAssetPath(
                    'System',
                    static::ASSETS_PATH_CSS,
                    trim($file)
                ),
            );
        }

        $this->addFiles(
            $this->fileResolver->getStaticAssetPath(
                'System',
                static::ASSETS_PATH_CSS,
                'layout.css'
            ),
        );
    }

    /**
     * Fetches the stylesheets of all currently enabled modules.
     */
    protected function fetchModuleStylesheets(): void
    {
        $area = $this->request->getArea();

        foreach ($this->modules->getInstalledModules() as $module) {
            $this->addFiles(
                $this->fileResolver->getStaticAssetPath(
                    $module['name'],
                    static::ASSETS_PATH_CSS,
                    'style.css'
                ),
            );

            if ($area === AreaEnum::AREA_ADMIN) {
                $this->addFiles(
                    $this->fileResolver->getStaticAssetPath(
                        $module['name'],
                        static::ASSETS_PATH_CSS,
                        'admin.css'
                    ),
                );
            }

            // Append custom styles to the default module styling
            $this->addFiles(
                $this->fileResolver->getStaticAssetPath(
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
        return array_reduce(
            array_filter($this->stylesheets, static fn ($stylesheet) => !empty($stylesheet)),
            fn ($accumulator, $stylesheet) => $accumulator . '<link rel="stylesheet" type="text/css" href="' . $this->fileResolver->rewriteToWebStaticAssetPath($stylesheet) . '">' . "\n",
            ''
        );
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    public function initialize(): void
    {
        $backup = $this->stylesheets;
        $this->stylesheets = [];

        // The sort order is important here, as module should be allowed to override the styles a library provides.
        // Also, themes should be allowed to override the styles modules defined.
        $this->assets->initializeTheme();
        $this->fetchLibraries();
        $this->fetchModuleStylesheets();
        $this->fetchThemeStylesheets();

        $this->addFiles($backup);
    }
}
