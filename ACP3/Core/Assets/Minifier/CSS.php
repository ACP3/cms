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
     * @inheritdoc
     */
    protected function processLibraries($layout)
    {
        $cacheId = $this->_buildCacheId('css', $layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $css = [];

            // At first, load the library stylesheets
            foreach ($this->assets->getLibraries() as $library) {
                if ($library['enabled'] === true && isset($library['css']) === true) {
                    $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_CSS, $library['css']);
                }
            }

            foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
                $css[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_CSS, trim($file));
            }

            // General system styles
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_CSS, 'style.css');
            // Stylesheet of the current theme
            $css[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_CSS, $layout . '.css');

            // Module stylesheets
            $modules = $this->modules->getActiveModules();
            foreach ($modules as $module) {
                $modulePath = $module['dir'] . '/Resources/';
                $designPath = $module['dir'] . '/';
                if (true == ($stylesheet = $this->themeResolver->getStaticAssetPath($modulePath, $designPath, static::ASSETS_PATH_CSS, 'style.css')) &&
                    $module['dir'] !== 'System'
                ) {
                    $css[] = $stylesheet;
                }

                // Append custom styles to the default module styling
                if (true == ($stylesheet = $this->themeResolver->getStaticAssetPath($modulePath, $designPath, static::ASSETS_PATH_CSS, 'append.css'))) {
                    $css[] = $stylesheet;
                }
            }

            $this->systemCache->save($cacheId, $css);
        }


        return $this->systemCache->fetch($cacheId);
    }

}