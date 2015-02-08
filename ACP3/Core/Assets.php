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
     * @var \ACP3\Core\Cache
     */
    protected $systemCache;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Assets\ThemeResolver
     */
    protected $themeResolver;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;

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
     * @var int
     */
    protected $currentTime = 0;

    /**
     * @param Cache         $assetsCache
     * @param Modules       $modules
     * @param Router        $router
     * @param ThemeResolver $themeResolver
     * @param Config        $config
     */
    public function __construct(
        Cache $assetsCache,
        Modules $modules,
        Router $router,
        ThemeResolver $themeResolver,
        Config $config
    )
    {
        $this->modules = $modules;
        $this->router = $router;
        $this->themeResolver = $themeResolver;
        $this->systemCache = $assetsCache;
        $this->config = $config;

        $this->currentTime = time();

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
        $cacheId = 'assets_';
        $cacheId .= $this->config->getSettings('system')['design'] . '_';
        $cacheId .= 'css_';
        $cacheId .= $this->_getJsLibrariesCache();

        if ($this->systemCache->contains($cacheId) === false) {
            $css = [];

            // At first, load the library stylesheets
            foreach ($this->libraries as $library) {
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

            $this->systemCache->save($cacheId, $css);
        }


        return $this->systemCache->fetch($cacheId);
    }

    /**
     * @param $layout
     *
     * @return array
     */
    public function includeJsFiles($layout)
    {
        $cacheId = 'assets_';
        $cacheId .= $this->config->getSettings('system')['design'] . '_';
        $cacheId .= 'js_';
        $cacheId .= $this->_getJsLibrariesCache();

        if ($this->systemCache->contains($cacheId) === false) {
            $scripts = [];
            foreach ($this->libraries as $library) {
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

            $this->systemCache->save($cacheId, $scripts);
        }

        return $this->systemCache->fetch($cacheId);
    }

    /**
     * Aktiviert einzelne Frontend Bibliotheken
     *
     * @param array $libraries
     *
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
    public function buildMinifyLink($group, $layout = 'layout')
    {
        $debug = (defined('DEBUG') && DEBUG === true);
        $filenameHash = $this->getFilenameHash($group, $layout);

        $cacheId = 'assets-last-generated-' . $filenameHash;

        if (false === ($lastGenerated = $this->systemCache->fetch($cacheId))) {
            $lastGenerated = $this->currentTime;
        }

        if ($debug === true) {
            $path = 'assets/' . $filenameHash . '.' . $group;
        } else {
            $path = 'assets/' . $filenameHash . '-' . $lastGenerated . '.' . $group;
        }

        // If the requested minified StyleSheet and/or the JavaScript file doesn't exist, generate it
        if (is_file(UPLOADS_DIR . $path) === false || $debug === true) {
            switch ($group) {
                case 'css':
                    $files = $this->includeCssFiles($layout);
                    break;
                case 'js':
                    $files = $this->includeJsFiles($layout);
                    break;
                default:
                    $files = [];
            }

            $files = array_filter($files, function ($var) {
                return !empty($var);
            });

            $options = [];
            $options['minifiers']['text/css'] = ['Minify_CSSmin', 'minify'];

            $content = \Minify::combine($files, $options);

            // Write the contents of the file to the uploads folder
            file_put_contents(UPLOADS_DIR . $path, $content, LOCK_EX);

            // Save the time of the generation if the requested file
            $this->systemCache->save($cacheId, $lastGenerated);
        }

        return ROOT_DIR . 'uploads/' . $path . ($debug === true ? '?v=' . $lastGenerated : '');
    }

    /**
     * @param $group
     * @param $layout
     *
     * @return string
     */
    private function getFilenameHash($group, $layout)
    {
        $filename = $this->config->getSettings('system')['design'];
        $filename .= '_' . $layout;
        $filename .= '_' . $this->_getJsLibrariesCache();
        $filename .= '_' . $group;

        return md5($filename);

    }

    /**
     * @return string
     */
    private function _getJsLibrariesCache()
    {
        if (empty($this->librariesCache)) {
            foreach ($this->libraries as $library => $values) {
                if ($values['enabled'] === true) {
                    $this->librariesCache .= $library . ',';
                }
            }
        }

        return $this->librariesCache;
    }
}
