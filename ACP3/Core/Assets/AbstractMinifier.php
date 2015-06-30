<?php
namespace ACP3\Core\Assets;

use ACP3\Core\Assets;
use ACP3\Core\Cache;
use ACP3\Core\Config;
use ACP3\Core\Modules;

/**
 * Class AbstractMinifier
 * @package ACP3\Core\Assets
 */
abstract class AbstractMinifier implements MinifierInterface
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
     * @var string
     */
    protected $assetGroup = '';
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
    protected function _buildCacheId($type, $layout)
    {
        return 'assets_' . $this->generateFilenameHash($type, $layout);
    }

    /**
     * @param $group
     * @param $layout
     *
     * @return string
     */
    protected function generateFilenameHash($group, $layout)
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
    abstract protected function processLibraries($layout);

    /**
     * Erstellt den Link zum Minifier mitsamt allen zu ladenden JavaScript Bibliotheken
     *
     * @param string $group
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
            $files = $this->processLibraries($layout);

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
     * @inheritdoc
     */
    public function getLink($layout = 'layout')
    {
        return $this->buildMinifyLink($this->assetGroup, $layout);
    }

}