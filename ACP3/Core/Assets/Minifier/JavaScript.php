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
     * @inheritdoc
     */
    protected function processLibraries($layout)
    {
        $cacheId = $this->_buildCacheId('js', $layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $scripts = [];
            foreach ($this->assets->getLibraries() as $library) {
                if ($library['enabled'] === true && isset($library['js']) === true) {
                    $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_JS_LIBS, $library['js']);
                }
            }

            // Include additional js files from the design
            foreach ($this->assets->fetchAdditionalThemeJsFiles() as $file) {
                $scripts[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_JS, $file);
            }

            // Include general js file of the layout
            $scripts[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_JS, $layout . '.js');

            $this->systemCache->save($cacheId, $scripts);
        }

        return $this->systemCache->fetch($cacheId);
    }

}