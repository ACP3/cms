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
     * Legt fest, welche Bibliotheken beim Seitenaufruf geladen werden sollen
     * @var array
     */
    protected $libraries = [
        'moment' => [
            'enabled' => false,
            'js' => 'moment.min.js'
        ],
        'jquery' => [
            'enabled' => true,
            'js' => 'jquery.min.js'
        ],
        'fancybox' => [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'jquery.fancybox.css',
            'js' => 'jquery.fancybox.min.js'
        ],
        'bootstrap' => [
            'enabled' => false,
            'dependencies' => ['jquery'],
            'css' => 'bootstrap.min.css',
            'js' => 'bootstrap.min.js'
        ],
        'datatables' => [
            'enabled' => false,
            'dependencies' => ['bootstrap'],
            'css' => 'dataTables.bootstrap.css',
            'js' => 'jquery.datatables.min.js'
        ],
        'bootbox' => [
            'enabled' => false,
            'dependencies' => ['bootstrap'],
            'js' => 'bootbox.min.js'
        ],
        'datetimepicker' => [
            'enabled' => false,
            'dependencies' => ['jquery', 'moment'],
            'css' => 'bootstrap-datetimepicker.css',
            'js' => 'bootstrap-datetimepicker.min.js'
        ],
    ];
    /**
     * @var string
     */
    protected $librariesCache = '';
    /**
     * @var string
     */
    protected $systemAssetsModulePath = 'System/Resources/';
    /**
     * @var string
     */
    protected $systemAssetsDesignPath = 'System/';
    /**
     * @var \SimpleXMLElement
     */
    protected $designXml;

    /**
     * @param \ACP3\Core\Modules              $modules
     * @param \ACP3\Core\Router               $router
     * @param \ACP3\Core\Assets\ThemeResolver $themeResolver
     * @param \ACP3\Core\Config               $systemConfig
     */
    public function __construct(
        Modules $modules,
        Router $router,
        ThemeResolver $themeResolver,
        Config $systemConfig
    ) {
        $this->modules = $modules;
        $this->router = $router;
        $this->themeResolver = $themeResolver;
        $this->systemConfig = $systemConfig->getSettings();

        $this->_checkBootstrap();
    }

    /**
     * Checks, whether the current design uses Twitter Bootstrap or not
     */
    private function _checkBootstrap()
    {
        $this->designXml = simplexml_load_file(DESIGN_PATH_INTERNAL . 'info.xml');

        if (isset($this->designXml->use_bootstrap) && (string)$this->designXml->use_bootstrap === 'true') {
            $this->enableLibraries(['bootstrap']);
        }
    }

    /**
     * @param $layout
     *
     * @return array
     */
    public function includeCssFiles($layout)
    {
        $css = [];

        // At first, load the library stylesheets
        foreach($this->libraries as $library) {
            if ($library['enabled'] === true && isset($library['css']) === true) {
                $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'Assets/css', $library['css']);
            }
        }

        if (isset($this->designXml->css)) {
            foreach ($this->designXml->css->item as $file) {
                $css[] = $this->themeResolver->getStaticAssetPath('', '', 'css', trim($file));
            }
        }

        // General system styles
        $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'Assets/css', 'style.css');
        // Stylesheet of the current theme
        $css[] = $this->themeResolver->getStaticAssetPath('', '', '', $layout . '.css');

        // Module stylesheets
        $modules = $this->modules->getActiveModules();
        foreach ($modules as $module) {
            $modulePath = $module['dir'] . '/Resources/';
            $designPath = $module['dir'] . '/';
            if (true == ($stylesheet = $this->themeResolver->getStaticAssetPath($modulePath, $designPath, 'Assets/css', 'style.css')) &&
                $module['dir'] !== 'System'
            ) {
                $css[] = $stylesheet;
            }

            // Append custom styles to the default module styling
            if (true == ($stylesheet = $this->themeResolver->getStaticAssetPath($modulePath, $designPath, 'Assets/css', 'append.css'))) {
                $css[] = $stylesheet;
            }
        }

        return $css;
    }

    /**
     * @param $layout
     *
     * @return array
     */
    public function includeJsFiles($layout)
    {
        $scripts = [];
        foreach($this->libraries as $library) {
            if ($library['enabled'] === true && isset($library['js']) === true) {
                $scripts[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, 'Assets/js/libs', $library['js']);
            }
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
     * Aktiviert einzelne Frontend Bibliotheken
     *
     * @param array $libraries
     * @return $this
     */
    public function enableLibraries(array $libraries)
    {
        foreach ($libraries as $library) {
            if (array_key_exists($library, $this->libraries) === true) {
                // Resolve javascript library dependencies recursively
                if (!empty($this->libraries[$library]['dependencies'])) {
                    $this->enableLibraries($this->libraries[$library]['dependencies']);
                }

                // Enabled the javascript library
                $this->libraries[$library]['enabled'] = true;
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
        if (empty($this->librariesCache)) {
            ksort($this->libraries);
            foreach ($this->libraries as $library => $values) {
                if ($values['enabled'] === true) {
                    $this->librariesCache .= $library . ',';
                }
            }
        }

        return $this->librariesCache;
    }
}
