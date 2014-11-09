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
     * @var Minify\Cache
     */
    protected $minifyCache;
    /**
     * @var array
     */
    protected $cachedPaths = array();
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
     * @param Minify\Cache $minifyCache
     */
    public function __construct(Minify\Cache $minifyCache)
    {
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

        // Return early, if the path has already been cached
        if (isset($this->cachedPaths[$systemAssetPath])) {
            return $this->cachedPaths[$systemAssetPath];
        } else {
            return $this->_resolveAssetPath($modulePath, $designPath, $dir, $file);
        }
    }

    /**
     * @param $modulePath
     * @param $designPath
     * @param $dir
     * @param $file
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
            $designInfo = Core\XML::parseXmlFile($this->designAssetsPath . '/info.xml', '/design');

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
}