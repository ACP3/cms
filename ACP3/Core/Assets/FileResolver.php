<?php
namespace ACP3\Core\Assets;

use ACP3\Core;

/**
 * Class FileResolver
 * @package ACP3\Core\Assets
 */
class FileResolver
{
    /**
     * @var \ACP3\Core\XML
     */
    protected $xml;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Assets\Cache
     */
    protected $resourcesCache;
    /**
     * @var \ACP3\Core\Modules\Vendors
     */
    protected $vendors;
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
    protected $modulesAssetsPath;
    /**
     * @var string
     */
    protected $designAssetsPath;

    /**
     * @param \ACP3\Core\XML                         $xml
     * @param \ACP3\Core\Assets\Cache                $resourcesCache
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Modules\Vendors             $vendors
     */
    public function __construct(
        Core\XML $xml,
        Core\Assets\Cache $resourcesCache,
        Core\Environment\ApplicationPath $appPath,
        Core\Modules\Vendors $vendors
    )
    {
        $this->xml = $xml;
        $this->resourcesCache = $resourcesCache;
        $this->appPath = $appPath;
        $this->vendors = $vendors;
        $this->cachedPaths = $resourcesCache->getCache();

        $this->modulesAssetsPath = $appPath->getModulesDir();
        $this->designAssetsPath = $appPath->getDesignPathInternal();
    }

    /**
     * Write newly added assets paths into the cache
     */
    public function __destruct()
    {
        if ($this->newAssetPathsAdded === true) {
            $this->resourcesCache->saveCache($this->cachedPaths);
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
     * @param        $modulePath
     * @param        $designPath
     * @param string $dir
     * @param string $file
     *
     * @return string
     */
    private function _resolveAssetPath($modulePath, $designPath, $dir, $file)
    {
        $assetPath = '';
        $designAssetPath = $this->designAssetsPath . $designPath . $dir . $file;

        // A theme has overridden a static asset of a module
        if (is_file($designAssetPath) === true) {
            $assetPath = $designAssetPath;
        } else {
            $designInfo = $this->xml->parseXmlFile($this->designAssetsPath . '/info.xml', '/design');

            // Recursively iterate over the nested themes
            if (!empty($designInfo['parent'])) {
                $this->designAssetsPath = ACP3_ROOT_DIR . 'designs/' . $designInfo['parent'];
                $assetPath = $this->getStaticAssetPath($modulePath, $designPath, $dir, $file);
                $this->designAssetsPath = $this->appPath->getDesignPathInternal();
            }

            // No overrides have been found -> iterate over all possible module namespaces
            foreach (array_reverse($this->vendors->getVendors()) as $vendor) {
                $moduleAssetPath = $this->modulesAssetsPath . $vendor . '/' . $modulePath . $dir . $file;
                if (is_file($moduleAssetPath) === true) {
                    $assetPath = $moduleAssetPath;
                    break;
                }
            }
        }

        $systemAssetPath = $this->modulesAssetsPath . $modulePath . $dir . $file;
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
