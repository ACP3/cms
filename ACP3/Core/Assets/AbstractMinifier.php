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
    protected $environment;

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
     * @param \ACP3\Core\Assets               $assets
     * @param \ACP3\Core\Cache                $systemCache
     * @param \ACP3\Core\Config               $config
     * @param \ACP3\Core\Modules              $modules
     * @param \ACP3\Core\Assets\ThemeResolver $themeResolver
     * @param string                          $environment
     */
    public function __construct(
        Assets $assets,
        Cache $systemCache,
        Config $config,
        Modules $modules,
        ThemeResolver $themeResolver,
        $environment
    )
    {
        $this->assets = $assets;
        $this->systemCache = $systemCache;
        $this->config = $config;
        $this->modules = $modules;
        $this->themeResolver = $themeResolver;
        $this->environment = $environment;
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
     * @param string $group
     * @param string $layout
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
     * @param string $layout
     *
     * @return array
     */
    abstract protected function processLibraries($layout);

    /**
     * @inheritdoc
     */
    public function getURI($layout = 'layout')
    {
        $debug = $this->environment === 'dev';
        $filenameHash = $this->generateFilenameHash($this->assetGroup, $layout);
        $cacheId = 'assets-last-generated-' . $filenameHash;

        if (false === ($lastGenerated = $this->systemCache->fetch($cacheId))) {
            $lastGenerated = time(); // Assets are not cached -> set the current time as the new timestamp
        }

        $path = $this->buildAssetPath($debug, $this->assetGroup, $filenameHash, $lastGenerated);

        // If the requested minified StyleSheet and/or the JavaScript file doesn't exist, generate it
        if (is_file(UPLOADS_DIR . $path) === false || $debug === true) {
            // Get the enabled libraries and filter out empty entries
            $files = array_filter(
                $this->processLibraries($layout),
                function ($var) {
                    return !empty($var);
                }
            );

            $this->saveMinifiedAsset($files, UPLOADS_DIR . $path);

            // Save the time of the generation if the requested file
            $this->systemCache->save($cacheId, $lastGenerated);
        }

        return ROOT_DIR . 'uploads/' . $path . ($debug === true ? '?v=' . $lastGenerated : '');
    }

    /**
     * @param array  $files
     * @param string $path
     */
    protected function saveMinifiedAsset($files, $path)
    {
        $options = [];
        $options['minifiers']['text/css'] = ['Minify_CSSmin', 'minify'];

        $content = \Minify::combine($files, $options);

        // Write the contents of the file to the uploads folder
        file_put_contents($path, $content, LOCK_EX);
    }

    /**
     * @param bool   $debug
     * @param string $group
     * @param string $filenameHash
     * @param int    $lastGenerated
     *
     * @return string
     */
    protected function buildAssetPath($debug, $group, $filenameHash, $lastGenerated)
    {
        if ($debug === true) {
            return 'assets/' . $filenameHash . '.' . $group;
        }

        return 'assets/' . $filenameHash . '-' . $lastGenerated . '.' . $group;
    }

}