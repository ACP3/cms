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
     * @var array
     */
    protected $systemConfig = [];

    /**
     * Legt fest, welche JavaScript Bibliotheken beim Seitenaufruf geladen werden sollen
     * @var array
     */
    protected $jsLibraries = [
        'datatables' => [
            'enabled' => false,
            'dependencies' => ['jquery']
        ],
        'datetimepicker' => [
            'enabled' => false,
            'dependencies' => ['jquery', 'moment']
        ],
        'bootbox' => [
            'enabled' => false,
            'dependencies' => ['bootstrap']
        ],
        'bootstrap' => [
            'enabled' => false,
            'dependencies' => ['jquery']
        ],
        'fancybox' => [
            'enabled' => false,
            'dependencies' => ['jquery']
        ],
        'jquery' => [
            'enabled' => true
        ],
        'moment' => [
            'enabled' => false,
            'dependencies' => []
        ],
    ];
    /**
     * @var string
     */
    protected $jsLibrariesCache = '';
    /**
     * @var string
     */
    protected $systemAssetsModulePath = 'System/Resources/Assets/';
    /**
     * @var string
     */
    protected $systemAssetsDesignPath = 'System/';
    /**
     * @var \SimpleXMLElement
     */
    protected $designXml;

    /**
     * @param Modules $modules
     * @param Router $router
     * @param ThemeResolver $themeResolver
     */
    public function __construct(
        Modules $modules,
        Router $router,
        ThemeResolver $themeResolver,
        Config $systemConfig
    )
    {
        $this->modules = $modules;
        $this->router = $router;
        $this->themeResolver = $themeResolver;
        $this->systemConfig = $systemConfig->getSettings();

        $this->_checkBootstrap();
    }

    /**
     * Checks, whether the current design should use bootstrap or not
     */
    private function _checkBootstrap()
    {
        $this->designXml = simplexml_load_file(DESIGN_PATH_INTERNAL . 'info.xml');

        if (isset($this->designXml->use_bootstrap) && (string)$this->designXml->use_bootstrap === 'true') {
            $this->enableJsLibraries(['bootstrap']);
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
        $css = [];

        if (in_array('bootstrap', $libraries)) {
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'bootstrap.min.css');
        }

        if (isset($this->designXml->css)) {
            foreach ($this->designXml->css->item as $file) {
                $css[] = $this->themeResolver->getStaticAssetPath('', '', 'css', trim($file));
            }
        }

        // Stylesheets der Bibliotheken zuerst laden,
        // damit deren Styles überschrieben werden können
        if (in_array('datetimepicker', $libraries)) {
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'bootstrap-datetimepicker.css');
        }
        if (in_array('fancybox', $libraries)) {
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'jquery.fancybox.css');
        }
        if (in_array('datatables', $libraries)) {
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'dataTables.bootstrap.css');
        }

        // General system styles
        $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'css', 'style.css');
        // Stylesheet of the current theme
        $css[] = $this->themeResolver->getStaticAssetPath('', '', '', $layout . '.css');

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
        $scripts = [];
        if (in_array('jquery', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.min.js');
        }

        if (in_array('bootstrap', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'bootstrap.min.js');
        }

        // JS-Libraries to include
        if (in_array('bootbox', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'bootbox.min.js');
        }
        if (in_array('moment', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'moment.min.js');
        }
        if (in_array('datetimepicker', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'bootstrap-datetimepicker.min.js');
        }
        if (in_array('fancybox', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.fancybox.min.js');
        }
        if (in_array('datatables', $libraries)) {
            $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'js/libs', 'jquery.datatables.min.js');
        }

        // Include additional js files from the design
        if (isset($this->designXml->js)) {
            foreach ($this->designXml->js->item as $js) {
                $scripts[] = $this->themeResolver->getStaticAssetPath('', '', 'js', $js);
            }
        }

        // Include general js file of the layout
        $scripts[] = $this->themeResolver->getStaticAssetPath('', '', '', $layout . '.js');

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

                // Resolve javascript library dependencies recursively
                if (!empty($this->jsLibraries[$library]['dependencies'])) {
                    $this->enableJsLibraries($this->jsLibraries[$library]['dependencies']);
                }

                // Enabled the javascript library
                $this->jsLibraries[$library]['enabled'] = true;
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

        return $this->router->route('minify/index/index/group_' . $group . '/design_' . $this->systemConfig['design'] . $layout . $libraries);
    }

    /**
     * @return string
     */
    private function _getJsLibrariesCache()
    {
        if (empty($this->jsLibrariesCache)) {
            ksort($this->jsLibraries);
            foreach ($this->jsLibraries as $library => $values) {
                if ($values['enabled'] === true) {
                    $this->jsLibrariesCache .= $library . ',';
                }
            }
        }

        return $this->jsLibrariesCache;

    }
}