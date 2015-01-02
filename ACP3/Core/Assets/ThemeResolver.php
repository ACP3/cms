<?php
namespace ACP3\Core\Assets;

use ACP3\Core;
use ACP3\Modules\Minify;

/**
 * Class ThemeResolver
 * @package ACP3\Core\Assets
 */
class ThemeResolver
{
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Modules\Minify\Cache
     */
    protected $minifyCache;
    /**
     * @var array
     */
    protected $cachedPaths = [];
    /**
     * @var bool
     */
    protected $newAssetPathsAdded = false;
    /**
     * @var string
     */
    protected $modulesAssetsPath = MODULES_DIR;
    /**
     * @var string
     */
    protected $designAssetsPath = DESIGN_PATH_INTERNAL;

    /**
     * @param \ACP3\Core\XML             $xml
     * @param \ACP3\Modules\Minify\Cache $minifyCache
     */
    public function __construct(
        Core\XML $xml,
        Minify\Cache $minifyCache
    )
    {
        $this->xml = $xml;
        $this->minifyCache = $minifyCache;
        $this->cachedPaths = $minifyCache->getCache();
    }

    /**
     * Write newly added assets paths into the cache
     */
    public function __destruct()
    {
        if ($this->newAssetPathsAdded === true) {
            $this->minifyCache->setCache($this->cachedPaths);
        }
    }

    /**
     * @param $modulePath
     * @param $designPath
     * @param $dir
     * @param $file
     *
     * @return string
     */
    public function getStaticAssetPath($modulePath, $designPath, $dir = '', $file = '')
    {
        if (strpos($modulePath, '.') === false && !preg_match('=/$=', $modulePath)) {
            $modulePath .= '/';
        }
        if (strpos($designPath, '.') === false && !preg_match('=/$=', $designPath)) {
            $designPath .= '/';
        }
        if (!empty($dir) && !preg_match('=/$=', $dir)) {
            $dir .= '/';
        }

        $systemAssetPath = $this->modulesAssetsPath . $modulePath . $dir . $file;

        // Return early, if the path has been already cached
        if (isset($this->cachedPaths[$systemAssetPath])) {
            return $this->cachedPaths[$systemAssetPath];
        }

        return $this->_resolveAssetPath($modulePath, $designPath, $dir, $file);
    }

    /**
     * @param $modulePath
     * @param $designPath
     * @param $dir
     * @param $file
     *
     * @return string
     */
    private function _resolveAssetPath($modulePath, $designPath, $dir, $file)
    {
        $assetPath = '';
        $systemAssetPath = $this->modulesAssetsPath . $modulePath . $dir . $file;
        $designAssetPath = $this->designAssetsPath . $designPath . $dir . $file;

        if (is_file($designAssetPath) === true) {
            $assetPath = $designAssetPath;
        } else {
            $designInfo = $this->xml->parseXmlFile($this->designAssetsPath . '/info.xml', '/design');

            if (!empty($designInfo['parent'])) {
                $this->designAssetsPath = ACP3_ROOT_DIR . 'designs/' . $designInfo['parent'];
                $assetPath = $this->getStaticAssetPath($modulePath, $designPath, $dir, $file);
                $this->designAssetsPath = DESIGN_PATH_INTERNAL;
            } elseif (is_file($systemAssetPath) === true) {
                $assetPath = $systemAssetPath;
            }
        }

        $this->cachedPaths[$systemAssetPath] = $assetPath;
        $this->newAssetPathsAdded = true;

        return $assetPath;
    }

    /**
     * @param $template
     *
     * @return string
     */
    public function resolveTemplatePath($template)
    {
        $modulesPath = '';

        // A path without any slash was given -> has to be the layout file of the current design
        if (strpos($template, '/') === false) {
            return $this->getStaticAssetPath($modulesPath, '', '', $template);
        } else {
            // Split the template path in its components
            $fragments = explode('/', ucfirst($template));

            if (isset($fragments[2])) {
                $fragments[1] = ucfirst($fragments[1]);
            }
            $modulesPath .= $fragments[0] . '/Resources/';
            $designPath = $fragments[0];
            $template = $fragments[1];
            if (isset($fragments[2])) {
                $template .= '/' . $fragments[2];
            }

            return $this->getStaticAssetPath($modulesPath, $designPath, 'View', $template);
        }
    }
}
