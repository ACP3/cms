<?php
namespace ACP3\Core\Assets;

use ACP3\Core\Assets;
use ACP3\Core\Cache;
use ACP3\Core\Config;
use ACP3\Core\Modules;

/**
 * Class Minifier
 * @package ACP3\Core\Assets
 */
class Minifier
{
    const ASSETS_PATH_CSS = 'Assets/css';
    const ASSETS_PATH_JS = 'Assets/js';
    const ASSETS_PATH_JS_LIBS = 'Assets/js/libs';

    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Cache
     */
    protected $systemCache;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Assets\ThemeResolver
     */
    protected $themeResolver;

    /**
     * @var string
     */
    protected $systemAssetsModulePath = 'System/Resources/';
    /**
     * @var string
     */
    protected $systemAssetsDesignPath = 'System/';
    /**
     * @var int
     */
    protected $currentTime = 0;

    /**
     * @param \ACP3\Core\Assets               $assets
     * @param \ACP3\Core\Cache                $systemCache
     * @param \ACP3\Core\Config               $config
     * @param \ACP3\Core\Modules              $modules
     * @param \ACP3\Core\Assets\ThemeResolver $themeResolver
     */
    public function __construct(
        Assets $assets,
        Cache $systemCache,
        Config $config,
        Modules $modules,
        ThemeResolver $themeResolver
    )
    {
        $this->assets = $assets;
        $this->systemCache = $systemCache;
        $this->config = $config;
        $this->modules = $modules;
        $this->themeResolver = $themeResolver;

        $this->currentTime = time();
    }

    /**
     * @return int
     */
    protected function getCurrentTime()
    {
        return $this->currentTime;
    }

    /**
     * @param string $type
     * @param string $layout
     *
     * @return string
     */
    private function _buildCacheId($type, $layout)
    {
        return 'assets_' . $this->generateFilenameHash($type, $layout);
    }

    /**
     * @param $group
     * @param $layout
     *
     * @return string
     */
    public function generateFilenameHash($group, $layout)
    {
        $filename = $this->config->getSettings('system')['design'];
        $filename .= '_' . $layout;
        $filename .= '_' . $this->assets->getEnabledLibrariesAsString();
        $filename .= '_' . $group;

        return md5($filename);
    }


    /**
     * @param $layout
     *
     * @return array
     */
    public function includeCssFiles($layout)
    {
        $cacheId = $this->_buildCacheId('css', $layout);

        if ($this->systemCache->contains($cacheId) === false) {
            $css = [];

            // At first, load the library stylesheets
            foreach ($this->assets->getLibraries() as $library) {
                if ($library['enabled'] === true && isset($library['css']) === true) {
                    $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_CSS, $library['css']);
                }
            }

            foreach ($this->assets->fetchAdditionalThemeCssFiles() as $file) {
                $css[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_CSS, trim($file));
            }

            // General system styles
            $css[] = $this->themeResolver->getStaticAssetPath($this->systemAssetsModulePath, $this->systemAssetsDesignPath, static::ASSETS_PATH_CSS, 'style.css');
            // Stylesheet of the current theme
            $css[] = $this->themeResolver->getStaticAssetPath('', '', static::ASSETS_PATH_CSS, $layout . '.css');

            // Module stylesheets
            $modules = $this->modules->getActiveModules();
            foreach ($modules as $module) {
                $modulePath = $module['dir'] . '/Resources/';
                $designPath = $module['dir'] . '/';
                if (true == ($stylesheet = $this->themeResolver->getStaticAssetPath($modulePath, $designPath, static::ASSETS_PATH_CSS, 'style.css')) &&
                    $module['dir'] !== 'System'
                ) {
                    $css[] = $stylesheet;
                }

                // Append custom styles to the default module styling
                if (true == ($stylesheet = $this->themeResolver->getStaticAssetPath($modulePath, $designPath, static::ASSETS_PATH_CSS, 'append.css'))) {
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


    /**
     * Erstellt den Link zum Minifier mitsamt allen zu ladenden JavaScript Bibliotheken
     *
     * @param        $group
     * @param string $layout
     *
     * @return string
     */
    protected function buildMinifyLink($group, $layout = 'layout')
    {
        $debug = (defined('DEBUG') && DEBUG === true);
        $filenameHash = $this->generateFilenameHash($group, $layout);

        $cacheId = 'assets-last-generated-' . $filenameHash;

        if (false === ($lastGenerated = $this->systemCache->fetch($cacheId))) {
            $lastGenerated = $this->getCurrentTime();
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
     * @param string $layout
     *
     * @return string
     */
    public function buildMinifiedCssLink($layout = 'layout')
    {
        return $this->buildMinifyLink('css', $layout);
    }

    /**
     * @param string $layout
     *
     * @return string
     */
    public function buildMinifiedJsLink($layout = 'layout')
    {
        return $this->buildMinifyLink('js', $layout);
    }

}