<?php
namespace ACP3\Core\Assets\Minifier;

use ACP3\Core\Assets\AbstractMinifier;

/**
 * Class JavaScript
 * @package ACP3\Core\Assets\Minifier
 */
class JavaScript extends AbstractMinifier
{
    /**
     * @var string
     */
    protected $assetGroup = 'js';
    /**
     * @var array
     */
    protected $javascript = [];

    /**
     * @inheritdoc
     */
    protected function processLibraries($layout)
    {
        $cacheId = $this->_buildCacheId('js', $layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $this->fetchLibraries();
            $this->fetchThemeJavaScript($layout);

            $this->systemCache->save($cacheId, $this->javascript);
        }

        return $this->systemCache->fetch($cacheId);
    }

    /**
     * Fetches the javascript files of all enabled frontend frameworks/libraries
     */
    protected function fetchLibraries()
    {
        foreach ($this->assets->getLibraries() as $library) {
            if ($library['enabled'] === true && isset($library['js']) === true) {
                $this->javascript[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_JS_LIBS, $library['js']);
            }
        }
    }

    /**
     * Fetches the theme javascript files
     *
     * @param $layout
     */
    protected function fetchThemeJavaScript($layout)
    {
        foreach ($this->assets->fetchAdditionalThemeJsFiles() as $file) {
            $this->javascript[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_JS, $file);
        }

        // Include general js file of the layout
        $this->javascript[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_JS, $layout . '.js');
    }

}