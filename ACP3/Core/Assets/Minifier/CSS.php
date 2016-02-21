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
        $cacheId = $this->buildCacheId('css', $layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $this->fetchLibraries();
            $this->fetchThemeStylesheets($layout);
            $this->fetchModuleStylesheets();

            $this->systemCache->save($cacheId, $this->stylesheets);
        }


        return $this->systemCache->fetch($cacheId);
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
            if ('' !== ($stylesheet = $this->fileResolver->getStaticAssetPath($modulePath, $designPath, static::ASSETS_PATH_CSS, 'style.css')) &&
                $module['dir'] !== 'System'
            ) {
                $this->stylesheets[] = $stylesheet;
            }

            // Append custom styles to the default module styling
            if ('' !== ($stylesheet = $this->fileResolver->getStaticAssetPath($modulePath, $designPath, static::ASSETS_PATH_CSS, 'append.css'))) {
                $this->stylesheets[] = $stylesheet;
            }
        }
    }

    /**
     * Fetch all stylesheets of the enabled frontend frameworks/libraries
     */
    protected function fetchLibraries()
    {
        foreach ($this->assets->getLibraries() as $library) {
            if ($library['enabled'] === true && isset($library['css']) === true) {
                $this->stylesheets[] = $this->fileResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_CSS, $library['css']);
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
            $this->stylesheets[] = $this->fileResolver->getStaticAssetPath('', '', static::ASSETS_PATH_CSS, trim($file));
        }

        // Include general system styles and the stylesheet of the current theme
        $this->stylesheets[] = $this->fileResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_CSS, 'style.css');
        $this->stylesheets[] = $this->fileResolver->getStaticAssetPath('', '', static::ASSETS_PATH_CSS, $layout . '.css');
    }

}