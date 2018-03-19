<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Minifier;

class CSS extends AbstractMinifier
{
    /**
     * @var array
     */
    protected $stylesheets = [];

    protected function getAssetGroup(): string
    {
        return 'css';
    }

    /**
     * {@inheritdoc}
     */
    protected function processLibraries(string $layout): array
    {
        $cacheId = $this->buildCacheId($this->getAssetGroup(), $layout);

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
     */
    protected function fetchLibraries(): void
    {
        foreach ($this->assets->getLibraries() as $library) {
            if ($library['enabled'] === false || isset($library[$this->getAssetGroup()]) === false) {
                continue;
            }

            $stylesheets = $library[$this->getAssetGroup()];
            if (!\is_array($stylesheets)) {
                $stylesheets = [$stylesheets];
            }

            foreach ($stylesheets as $stylesheet) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                    !empty($library['module']) ? $library['module'] . '/Resources' : $this->systemAssetsModulePath,
                    $library['module'] ?? $this->systemAssetsDesignPath,
                    static::ASSETS_PATH_CSS,
                    $stylesheet
                );
            }
        }
    }

    /**
     * Fetches the theme stylesheets.
     *
     * @param string $layout
     */
    protected function fetchThemeStylesheets(string $layout): void
    {
        foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
            $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                '',
                '',
                static::ASSETS_PATH_CSS,
                \trim($file)
            );
        }

        // Include general system styles and the stylesheet of the current theme
        $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
            $this->systemAssetsModulePath,
            $this->systemAssetsDesignPath,
            static::ASSETS_PATH_CSS,
            'style.css'
        );
        $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
            '',
            '',
            static::ASSETS_PATH_CSS,
            $layout . '.css'
        );
    }

    /**
     * Fetches the stylesheets of all currently enabled modules.
     */
    protected function fetchModuleStylesheets(): void
    {
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $module) {
            $modulePath = $module['dir'] . '/Resources/';
            $designPath = $module['dir'] . '/';

            $stylesheet = $this->fileResolver->getStaticAssetPath(
                $modulePath,
                $designPath,
                static::ASSETS_PATH_CSS,
                'style.css'
            );
            if ('' !== $stylesheet && $module['dir'] !== 'System') {
                $this->stylesheets[] = $stylesheet;
            }

            // Append custom styles to the default module styling
            $appendStylesheet = $this->fileResolver->getStaticAssetPath(
                $modulePath,
                $designPath,
                static::ASSETS_PATH_CSS,
                'append.css'
            );
            if ('' !== $appendStylesheet) {
                $this->stylesheets[] = $appendStylesheet;
            }
        }
    }
}
