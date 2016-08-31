<?php
namespace ACP3\Core\Assets\Minifier;

use ACP3\Core\Assets\AbstractMinifier;

/**
 * Class CSS
 * @package ACP3\Core\Assets\Minifier
 */
class CSS extends AbstractMinifier
{
    /**
     * @var string
     */
    protected $assetGroup = 'css';
    /**
     * @var array
     */
    protected $stylesheets = [];

    /**
     * @inheritdoc
     */
    protected function processLibraries($layout)
    {
        $cacheId = $this->buildCacheId($this->assetGroup, $layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $this->fetchLibraries();
            $this->fetchThemeStylesheets($layout);
            $this->fetchModuleStylesheets();

            $this->systemCache->save($cacheId, $this->stylesheets);
        }

        return $this->systemCache->fetch($cacheId);
    }

    /**
     * Fetch all stylesheets of the enabled frontend frameworks/libraries
     */
    protected function fetchLibraries()
    {
        foreach ($this->assets->getLibraries() as $library) {
            if ($library['enabled'] === true && isset($library[$this->assetGroup]) === true) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath(
                    !empty($library['module']) ? $library['module'] . '/Resources' : $this->systemAssetsModulePath,
                    !empty($library['module']) ? $library['module'] : $this->systemAssetsDesignPath,
                    static::ASSETS_PATH_CSS,
                    $library[$this->assetGroup]
                );
            }
        }
    }

    /**
     * Fetches the theme stylesheets
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
                trim($file)
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
     * Fetches the stylesheets of all currently enabled modules
     */
    protected function fetchModuleStylesheets()
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
