<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Renderer\Strategies;

use ACP3\Core\Assets;
use ACP3\Core\Assets\Entity\LibraryEntity;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use tubalmartin\CssMin\Minifier;

class ConcatCSSRendererStrategy extends AbstractConcatRendererStrategy implements CSSRendererStrategyInterface
{
    protected const ASSETS_PATH_CSS = 'Assets/css';

    /**
     * @var \tubalmartin\CssMin\Minifier
     */
    private $minifier;

    /**
     * @var array
     */
    protected $stylesheets = [];

    public function __construct(
        Minifier $minifier,
        Assets $assets,
        Assets\Libraries $libraries,
        ApplicationPath $appPath,
        Cache $systemCache,
        SettingsInterface $config,
        Modules $modules,
        FileResolver $fileResolver
    ) {
        parent::__construct($assets, $libraries, $appPath, $systemCache, $config, $modules, $fileResolver);

        $this->minifier = $minifier;
    }

    protected function getAssetGroup(): string
    {
        return 'css';
    }

    protected function getFileExtension(): string
    {
        return 'css';
    }

    /**
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function getEnabledLibrariesAsString(): string
    {
        return implode(',', array_map(static function (LibraryEntity $library) {
            return $library->getLibraryIdentifier();
        }, $this->getEnabledLibraries()));
    }

    /**
     * @return LibraryEntity[]
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function getEnabledLibraries(): array
    {
        return array_filter($this->libraries->getEnabledLibraries(), static function (LibraryEntity $library) {
            return $library->getCss() && !$library->isDeferrableCss();
        });
    }

    /**
     * {@inheritdoc}
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    protected function processLibraries(string $layout): array
    {
        $cacheId = $this->buildCacheId($layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $this->fetchLibraries();
            $this->fetchThemeStylesheets($layout);
            $this->fetchModuleStylesheets();

            $this->systemCache->save($cacheId, $this->stylesheets);
        }

        return $this->systemCache->fetch($cacheId);
    }

    /**
     * Fetch all stylesheets of the enabled frontend frameworks/libraries.
     *
     * @throws \MJS\TopSort\CircularDependencyException
     * @throws \MJS\TopSort\ElementNotFoundException
     */
    private function fetchLibraries(): void
    {
        foreach ($this->getEnabledLibraries() as $library) {
            foreach ($library->getCss() as $stylesheet) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
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
            $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                '',
                static::ASSETS_PATH_CSS,
                trim($file)
            );
        }

        $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
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
            $stylesheet = $this->fileResolver->getStaticAssetPath(
                $module['name'],
                static::ASSETS_PATH_CSS,
                'style.css'
            );
            if ('' !== $stylesheet) {
                $this->stylesheets[] = $stylesheet;
            }

            // Append custom styles to the default module styling
            $appendStylesheet = $this->fileResolver->getStaticAssetPath(
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
        return '<link rel="stylesheet" type="text/css" href="' . $this->getURI($layout) . '">' . "\n";
    }

    protected function compress(string $assetContent): string
    {
        return $this->minifier->run($assetContent);
    }
}
