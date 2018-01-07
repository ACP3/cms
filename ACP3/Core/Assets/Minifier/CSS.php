<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Assets\Minifier;

class CSS extends AbstractMinifier
{
    const ASSETS_PATH_CSS = 'Assets/css';

    /**
     * @var array
     */
    private $stylesheets = [];

    protected function getAssetGroup(): string
    {
        return 'css';
    }

    /**
     * {@inheritdoc}
     */
    protected function processLibraries(string $layout)
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
    protected function fetchLibraries()
    {
        foreach ($this->assets->getLibraries() as $library) {
            if ($library['enabled'] === true && isset($library[$this->getAssetGroup()]) === true) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                    !empty($library['module']) ? $library['module'] . '/Resources' : $this->systemAssetsModulePath,
                    !empty($library['module']) ? $library['module'] : $this->systemAssetsDesignPath,
                    static::ASSETS_PATH_CSS,
                    $library[$this->getAssetGroup()]
                );
            }
        }
    }

    /**
     * Fetches the theme stylesheets.
     *
     * @param string $layout
     */
    protected function fetchThemeStylesheets($layout)
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
    protected function fetchModuleStylesheets()
    {
        $stylesheetNames = ['admin.css', 'widget.css', 'style.css', 'append.css'];
        foreach ($this->modules->getActiveModules() as $module) {
            $modulePath = $module['dir'] . '/Resources/';
            $designPath = $module['dir'] . '/';

            foreach ($stylesheetNames as $fileName) {
                if ($fileName === 'style.css' && $module['dir'] === 'System') {
                    continue;
                }

                $stylesheet = $this->fileResolver->getStaticAssetPath(
                    $modulePath,
                    $designPath,
                    static::ASSETS_PATH_CSS,
                    $fileName
                );
                if ('' !== $stylesheet) {
                    $this->stylesheets[] = $stylesheet;
                }
            }
        }
    }
}
