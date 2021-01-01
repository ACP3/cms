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

    protected function getFileExtension(): string
    {
        return 'css';
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
        foreach ($this->libraries->getLibraries() as $library) {
            if ($library->isEnabled() === false || !$library->getCss() || $library->isDeferrableCss()) {
                continue;
            }

            foreach ($library->getCss() as $stylesheet) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                    $library->getModuleName() ?: static::SYSTEM_MODULE_NAME,
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
                \trim($file)
            );
        }

        // Include general system styles and the stylesheet of the current theme
        $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
            static::SYSTEM_MODULE_NAME,
            static::ASSETS_PATH_CSS,
            'style.css'
        );
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
        foreach ($this->modules->getActiveModules() as $module) {
            $stylesheet = $this->fileResolver->getStaticAssetPath(
                $module['name'],
                static::ASSETS_PATH_CSS,
                'style.css'
            );
            if ('' !== $stylesheet && $module['name'] !== self::SYSTEM_MODULE_NAME) {
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
}
