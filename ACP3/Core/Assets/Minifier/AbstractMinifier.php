<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Assets\Minifier;

use ACP3\Core\Assets;
use ACP3\Core\Assets\FileResolver;
use ACP3\Core\Cache\Cache;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\System\Installer\Schema;
use JSMin\JSMin;
use Psr\Log\LoggerInterface;

abstract class AbstractMinifier implements MinifierInterface
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \ACP3\Core\Cache\Cache
     */
    protected $systemCache;
    /**
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Modules\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Assets\FileResolver
     */
    protected $fileResolver;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param LoggerInterface $logger
     * @param \ACP3\Core\Assets $assets
     * @param \ACP3\Core\Environment\ApplicationPath $appPath
     * @param \ACP3\Core\Cache\Cache $systemCache
     * @param SettingsInterface $config
     * @param \ACP3\Core\Modules\Modules $modules
     * @param \ACP3\Core\Assets\FileResolver $fileResolver
     * @param string $environment
     */
    public function __construct(
        LoggerInterface $logger,
        Assets $assets,
        ApplicationPath $appPath,
        Cache $systemCache,
        SettingsInterface $config,
        Modules $modules,
        FileResolver $fileResolver,
        $environment
    ) {
        $this->assets = $assets;
        $this->appPath = $appPath;
        $this->systemCache = $systemCache;
        $this->config = $config;
        $this->modules = $modules;
        $this->fileResolver = $fileResolver;
        $this->environment = $environment;
        $this->logger = $logger;
    }

    /**
     * Returns the name of the asset group
     *
     * @return string
     */
    abstract protected function getAssetGroup(): string;

    /**
     * @param string $type
     * @param string $layout
     *
     * @return string
     */
    protected function buildCacheId($type, $layout)
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
        $filename = $this->config->getSettings(Schema::MODULE_NAME)['design'];
        $filename .= '_' . $layout;
        $filename .= '_' . $this->assets->getEnabledLibrariesAsString();
        $filename .= '_' . $group;

        return md5($filename);
    }

    /**
     * @param string $layout
     * @return array
     */
    abstract protected function processLibraries(string $layout);

    /**
     * @inheritdoc
     */
    public function getURI(string $layout = 'layout')
    {
        $debug = $this->environment === 'dev';
        $filenameHash = $this->generateFilenameHash($this->getAssetGroup(), $layout);
        $cacheId = 'assets-last-generated-' . $filenameHash;

        if (false === ($lastGenerated = $this->systemCache->fetch($cacheId))) {
            $lastGenerated = time(); // Assets are not cached -> set the current time as the new timestamp
        }

        $path = $this->buildAssetPath($debug, $this->getAssetGroup(), $filenameHash, $lastGenerated);

        // If the requested minified StyleSheet and/or the JavaScript file doesn't exist, generate it
        if (is_file($this->appPath->getUploadsDir() . $path) === false || $debug === true) {
            // Get the enabled libraries and filter out empty entries
            $files = array_filter(
                $this->processLibraries($layout),
                function ($var) {
                    return !empty($var);
                }
            );

            $this->saveMinifiedAsset($files, $this->appPath->getUploadsDir() . $path);

            // Save the time of the generation if the requested file
            $this->systemCache->save($cacheId, $lastGenerated);
        }

        return $this->appPath->getWebRoot() . 'uploads/' . $path . ($debug === true ? '?v=' . $lastGenerated : '');
    }

    /**
     * @param array $files
     * @param string $path
     */
    protected function saveMinifiedAsset(array $files, $path)
    {
        $options = [
            'options' => [
                \Minify::TYPE_CSS => [\Minify_CSSmin::class, 'minify'],
                \Minify::TYPE_JS => [JSMin::class, 'minify'],
            ]
        ];

        $minify = new \Minify(new \Minify_Cache_Null(), $this->logger);
        $content = $minify->combine($files, $options);

        if (!is_dir($this->appPath->getUploadsDir() . 'assets')) {
            @mkdir($this->appPath->getUploadsDir() . 'assets', 0755);
        }

        // Write the contents of the file to the uploads folder
        file_put_contents($path, $content, LOCK_EX);
    }

    /**
     * @param bool $debug
     * @param string $group
     * @param string $filenameHash
     * @param int $lastGenerated
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
