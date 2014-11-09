<?php
namespace ACP3\Core;
use ACP3\Core\Assets\ThemeResolver;

/**
 * Class Assets
 * @package ACP3\Core
 */
class Assets
{
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var Router
     */
    protected $router;
    /**
     * @var ThemeResolver
     */
    protected $themeResolver;

    /**
     * Legt fest, welche JavaScript Bibliotheken beim Seitenaufruf geladen werden sollen
     * @var array
     */
    protected $jsLibraries = array(
        'bootbox' => false,
        'fancybox' => false,
        'jquery-ui' => false,
        'timepicker' => false,
        'datatables' => false
    );
    /**
     * @var string
     */
    protected $jsLibrariesCache = '';
    /**
     * @var string
     */
    protected $systemAssetsModulesPath = '';
    /**
     * @var string
     */
    protected $systemAssetsDesignPath = '';

    /**
     * @param Modules $modules
     * @param Router $router
     */
    public function __construct(
        Modules $modules,
        Router $router,
        ThemeResolver $themeResolver
    )
    {
        $this->modules = $modules;
        $this->router = $router;
        $this->themeResolver = $themeResolver;

        $this->systemAssetsModulePath = 'System/Resources/Assets/';
        $this->systemAssetsDesignPath = 'System/';
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
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'bootstrap.min.css');
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
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery-ui.css');
        }
        if (in_array('timepicker', $libraries)) {
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery-timepicker.css');
        }
        if (in_array('fancybox', $libraries)) {
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery.fancybox.css');
        }
        if (in_array('datatables', $libraries)) {
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'dataTables.bootstrap.css');
        }

        // Stylesheet für das Layout-Tenplate
        $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'style.css');
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
            if (true == ($stylesheet = $this->themeResolver->getStaticAssetPath($modulePath, $designPath, 'css', 'style.css')) &&
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
        $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.min.js');

        if (isset($xml->use_bootstrap) && (string)$xml->use_bootstrap === 'true') {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'bootstrap.min.js');
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
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'bootbox.min.js');
        }
        if (in_array('jquery-ui', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery-ui.min.js');
        }
        if (in_array('timepicker', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.timepicker.js');
        }
        if (in_array('fancybox', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.fancybox.min.js');
        }
        if (in_array('datatables', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.datatables.min.js');
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

    /**
     * Aktiviert einzelne JavaScript Bibliotheken
     *
     * @param array $libraries
     * @return $this
     */
    public function enableJsLibraries(array $libraries)
    {
        foreach ($libraries as $library) {
            if (array_key_exists($library, $this->jsLibraries) === true) {
                $this->jsLibraries[$library] = true;
                if ($library === 'timepicker') {
                    $this->jsLibraries['jquery-ui'] = true;
                }
            }
        }

        return $this;
    }

    /**
     * Erstellt den Link zum Minifier mitsamt allen zu ladenden JavaScript Bibliotheken
     *
     * @param        $group
     * @param string $layout
     *
     * @return string
     */
    public function buildMinifyLink($group, $layout = '')
    {
        if (!empty($layout)) {
            $layout = '/layout_' . $layout;
        }

        $libraries = $this->_getJsLibrariesCache();

        if ($libraries !== '') {
            $libraries = '/libraries_' . substr($libraries, 0, -1);
        }

        return $this->router->route('minify/index/index/group_' . $group . '/design_' . CONFIG_DESIGN . $layout . $libraries);
    }

    /**
     * @return string
     */
    private function _getJsLibrariesCache()
    {
        if (empty($this->jsLibrariesCache)) {
            ksort($this->jsLibraries);
            foreach ($this->jsLibraries as $library => $enable) {
                if ($enable === true) {
                    $this->jsLibrariesCache .= $library . ',';
                }
            }
        }

        return $this->jsLibrariesCache;

    }
}