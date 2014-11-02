<?php

/**
 * Minify
 *
 * @author     Tino Goratsch
 * @package    ACP3
 * @subpackage Modules
 */

namespace ACP3\Modules\Minify;

/**
 * Class Helpers
 * @package ACP3\Modules\Minify
 */
class Helpers
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var
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
    protected $systemAssetsModulesPath = '';
    /**
     * @var string
     */
    protected $systemAssetsDesignPath = '';

    /**
     * @param \ACP3\Core\Modules $modules
     * @param Cache $minifyCache
     */
    public function __construct(
        \ACP3\Core\Modules $modules,
        Cache $minifyCache
    )
    {
        $this->modules = $modules;
        $this->minifyCache = $minifyCache;

        $this->systemAssetsModulePath = MODULES_DIR . 'System/Resources/Assets/';
        $this->systemAssetsDesignPath = DESIGN_PATH_INTERNAL . 'System/';

        $this->cachedPaths = $minifyCache->getCache();
    }

    public function __destruct()
    {
        if ($this->newAssetPathsAdded === true) {
            $this->minifyCache->setCache($this->cachedPaths);
        }
    }

    /**
     *
     * @param string $libraries
     * @param string $layout
     *
     * @return array
     */
    public function includeCssFiles($libraries, $layout)
    {
        $xml = simplexml_load_file(DESIGN_PATH_INTERNAL . 'info.xml');

        $css = array();

        if (isset($xml->use_bootstrap) && (string)$xml->use_bootstrap === 'true') {
            $css[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'bootstrap.min.css');
        }

        if (isset($xml->css)) {
            foreach ($xml->css->item as $file) {
                $path = DESIGN_PATH_INTERNAL . 'css/' . $file;
                if (is_file($path) === true) {
                    $css[] = $path;
                }
            }
        }

        // Stylesheets der Bibliotheken zuerst laden,
        // damit deren Styles überschrieben werden können
        if (in_array('jquery-ui', $libraries)) {
            $css[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery-ui.css');
        }
        if (in_array('timepicker', $libraries)) {
            $css[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery-timepicker.css');
        }
        if (in_array('fancybox', $libraries)) {
            $css[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery.fancybox.css');
        }
        if (in_array('datatables', $libraries)) {
            $css[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery-datatables.css');
        }

        // Stylesheet für das Layout-Tenplate
        $css[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'style.css');
        $css[] = DESIGN_PATH_INTERNAL . (is_file(DESIGN_PATH_INTERNAL . $layout . '.css') === true ? $layout : 'layout') . '.css';

        // Zusätzliche Stylesheets einbinden
        $extraCss = explode(',', CONFIG_EXTRA_CSS);
        if (count($extraCss) > 0) {
            foreach ($extraCss as $file) {
                $path = DESIGN_PATH_INTERNAL . 'css/' . trim($file);
                if (is_file($path) && in_array($path, $css) === false) {
                    $css[] = $path;
                }
            }
        }

        // Stylesheets der Module
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $module) {
            $modulePath = MODULES_DIR . $module['dir'] . '/Resources/Assets/';
            $designPath = DESIGN_PATH_INTERNAL . $module['dir'] . '/';
            if (true == ($stylesheet = $this->getStaticAssetPath($modulePath, $designPath, 'css', 'style.css')) &&
                $module['dir'] !== 'System'
            ) {
                $css[] = $stylesheet;
            }
            // Append some custom styles to the default module styling
            $pathModuleAppend = $designPath . 'css/append.css';
            if (is_file($pathModuleAppend) === true) {
                $css[] = $pathModuleAppend;
            }
        }

        return $css;
    }

    /**
     * @param $systemPath
     * @param $designPath
     * @param $dir
     * @param $file
     *
     * @return string
     */
    public function getStaticAssetPath($systemPath, $designPath, $dir, $file)
    {
        if (!preg_match('=/$=', $systemPath)) {
            $systemPath .= '/';
        }
        if (!preg_match('=/$=', $designPath)) {
            $designPath .= '/';
        }
        if (!empty($dir) && !preg_match('=/$=', $dir)) {
            $dir .= '/';
        }

        $assetPath = '';
        $systemAssetPath = $systemPath . $dir . $file;
        $designAssetPath = $designPath . $dir . $file;

        // Return early, if the path has already been cached
        if (isset($this->cachedPaths[$systemAssetPath])) {
            return $this->cachedPaths[$systemAssetPath];
        } else {
            if (is_file($designAssetPath) === true) {
                $assetPath = $designAssetPath;
            } elseif (is_file($systemAssetPath) === true) {
                $assetPath = $systemAssetPath;
            }

            $this->cachedPaths[$systemAssetPath] = $assetPath;
            $this->newAssetPathsAdded = true;

            return $assetPath;
        }
    }

    /**
     *
     * @param string $libraries
     * @param string $layout
     *
     * @return array
     */
    public function includeJsFiles($libraries, $layout)
    {
        $xml = simplexml_load_file(DESIGN_PATH_INTERNAL . 'info.xml');

        $scripts = array();
        $scripts[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.min.js');

        if (isset($xml->use_bootstrap) && (string)$xml->use_bootstrap === 'true') {
            $scripts[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'bootstrap.min.js');
        }

        // Include js files from the design
        if (isset($xml->js)) {
            foreach ($xml->js->item as $js) {
                $path = DESIGN_PATH_INTERNAL . 'js/' . $js;
                if (is_file($path) === true) {
                    $scripts[] = $path;
                }
            }
        }

        // JS-Libraries to include
        if (in_array('bootbox', $libraries)) {
            $scripts[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'bootbox.min.js');
        }
        if (in_array('jquery-ui', $libraries)) {
            $scripts[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery-ui.min.js');
        }
        if (in_array('timepicker', $libraries)) {
            $scripts[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.timepicker.js');
        }
        if (in_array('fancybox', $libraries)) {
            $scripts[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.fancybox.min.js');
        }
        if (in_array('datatables', $libraries)) {
            $scripts[] = $this->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.datatables.min.js');
        }

        // Include general js file of the layout
        if (is_file(DESIGN_PATH_INTERNAL . $layout . '.js') === true) {
            $scripts[] = DESIGN_PATH_INTERNAL . $layout . '.js';
        }

        // Include additional js files from the system config
        $extraJs = explode(',', CONFIG_EXTRA_JS);
        if (count($extraJs) > 0) {
            foreach ($extraJs as $file) {
                $path = DESIGN_PATH_INTERNAL . 'js/' . trim($file);
                if (is_file($path) && in_array($path, $scripts) === false) {
                    $scripts[] = $path;
                }
            }
        }

        return $scripts;
    }
}