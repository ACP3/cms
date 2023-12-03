<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace RFM;

use Illuminate\Config\Repository;
use RFM\API\ApiInterface;
use RFM\Event\Api as ApiEvent;
use RFM\Repository\StorageInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\HttpFoundation\Request;

// path to "application" folder
\defined('FM_APP_PATH') or \define('FM_APP_PATH', __DIR__);
// path to PHP connector root folder
\defined('FM_ROOT_PATH') or \define('FM_ROOT_PATH', \dirname(__FILE__, 2));
// path to PHP connector root folder
\defined('DS') or \define('DS', DIRECTORY_SEPARATOR);

class Application
{
    /**
     * Active API instance.
     *
     * @var ApiInterface
     */
    public $api;

    /**
     * Prefix of RFM applicatioin for service container.
     *
     * @var string
     */
    public static $prefix = 'rfm';

    /**
     * The base path of the application installation.
     *
     * @var string
     */
    protected $basePath;

    /**
     * @var StorageInterface[]
     */
    protected static $storageRegistry = [];

    /**
     * All of the loaded configuration files.
     *
     * @var array
     */
    protected static $loadedConfigurations = [];

    /**
     * Application constructor.
     */
    public function __construct(string $basePath = null)
    {
        if (\is_null($basePath)) {
            $basePath = realpath(__DIR__);
        }

        $this->basePath = $basePath;

        $this->registerRFM();
        $this->registerConfigBindings();
        $this->registerLoggerBindings();
        $this->registerRequestBindings();
        $this->registerDispatcherBindings();

        if (\function_exists('fm_authenticate')) {
            $authenticated = fm_authenticate();

            if ($authenticated !== true) {
                $data = \is_array($authenticated) ? $authenticated : [];
                app()->error('AUTHORIZATION_REQUIRED', [], $data);
            }
        }
    }

    /**
     * Return source string prefixed with app prefix.
     */
    public function prefixed(string $string): string
    {
        return static::$prefix . '.' . $string;
    }

    /**
     * Add storage to the collection.
     */
    public function setStorage(StorageInterface $storage): void
    {
        $name = $storage->getName();

        static::$storageRegistry[$name] = $storage;
    }

    /**
     * Get storage from the collection by name.
     *
     * @throws \Exception
     */
    public function getStorage($name): StorageInterface
    {
        if (!isset(static::$storageRegistry[$name])) {
            throw new \Exception("Storage with name \"{$name}\" is not set.");
        }

        return static::$storageRegistry[$name];
    }

    /**
     * Load a configuration file into the application.
     */
    public function configure(string $name, array $options = []): void
    {
        if (isset(static::$loadedConfigurations[$name])) {
            return;
        }

        static::$loadedConfigurations[$name] = true;

        $path = $this->getConfigurationPath($name);

        if ($path) {
            $config = $this->mergeConfigs(require $path, $options);
            config([$name => $config]);

            // update logger configuration
            if (config("{$name}.logger.enabled") === true) {
                logger()->enabled = true;
            }
            if (\is_string(config("{$name}.logger.file"))) {
                logger()->file = config("{$name}.logger.file");
            }
        }
    }

    /**
     * Get the path to the given configuration file.
     */
    public function getConfigurationPath(string $name): string
    {
        return $this->basePath() . DS . 'config' . DS . "config.{$name}.php";
    }

    /**
     * Get the base path for the application.
     */
    public function basePath(string $path = null): string
    {
        if (isset($this->basePath)) {
            return $this->basePath . ($path ? '/' . $path : $path);
        }

        $this->basePath = \dirname(getcwd()) . '/';

        return $this->basePath($path);
    }

    /**
     * Register RichFilemanager application instance.
     */
    public function registerRFM(): void
    {
        container()->instance('richfilemanager', $this);
    }

    /**
     * Register request instance.
     */
    public function registerRequestBindings(): void
    {
        container()->singleton('request', function () {
            return Request::createFromGlobals();
        });
    }

    /**
     * Register logger instance.
     */
    public function registerLoggerBindings(): void
    {
        container()->singleton('logger', function () {
            return new Logger();
        });
    }

    /**
     * Register configuration repository instance.
     */
    public function registerConfigBindings(): void
    {
        container()->singleton('config', function () {
            return new Repository();
        });
    }

    /**
     * Register events dispatcher instance.
     */
    public function registerDispatcherBindings(): void
    {
        container()->singleton('dispatcher', function () {
            return new EventDispatcher();
        });
    }

    /**
     * Register events listeners.
     */
    public function registerEventsListeners(): void
    {
        dispatcher()->addListener(ApiEvent\AfterFolderReadEvent::NAME, 'fm_event_api_after_folder_read');
        dispatcher()->addListener(ApiEvent\AfterFolderSeekEvent::NAME, 'fm_event_api_after_folder_seek');
        dispatcher()->addListener(ApiEvent\AfterFolderCreateEvent::NAME, 'fm_event_api_after_folder_create');
        dispatcher()->addListener(ApiEvent\AfterFileUploadEvent::NAME, 'fm_event_api_after_file_upload');
        dispatcher()->addListener(ApiEvent\AfterFileExtractEvent::NAME, 'fm_event_api_after_file_extract');
        dispatcher()->addListener(ApiEvent\AfterItemRenameEvent::NAME, 'fm_event_api_after_item_rename');
        dispatcher()->addListener(ApiEvent\AfterItemCopyEvent::NAME, 'fm_event_api_after_item_copy');
        dispatcher()->addListener(ApiEvent\AfterItemMoveEvent::NAME, 'fm_event_api_after_item_move');
        dispatcher()->addListener(ApiEvent\AfterItemDeleteEvent::NAME, 'fm_event_api_after_item_delete');
        dispatcher()->addListener(ApiEvent\AfterItemDownloadEvent::NAME, 'fm_event_api_after_item_download');
    }

    /**
     * Invokes API action based on request params and returns response.
     *
     * @throws \Exception
     * @throws \Psr\Container\ContainerExceptionInterface
     */
    public function run(): void
    {
        if (\count(static::$storageRegistry) === 0) {
            throw new \Exception('No storage has been set.');
        }

        if (!($this->api instanceof ApiInterface)) {
            throw new \Exception('API has not been set.');
        }

        $response = null;
        $method = request()->getMethod();
        $mode = request()->get('mode');

        if (empty($mode)) {
            $this->error('MODE_ERROR');
        }

        switch ($mode) {
            case 'initiate':
                if ($method === 'GET') {
                    $response = $this->api->actionInitiate();
                }
                break;

            case 'getinfo':
                if ($method === 'GET' && request()->get('path')) {
                    $response = $this->api->actionGetInfo();
                }
                break;

            case 'readfolder':
                if ($method === 'GET' && request()->get('path')) {
                    $response = $this->api->actionReadFolder();
                }
                break;

            case 'seekfolder':
                if ($method === 'GET' && request()->get('path') && request()->get('string')) {
                    $response = $this->api->actionSeekFolder();
                }
                break;

            case 'rename':
                if ($method === 'GET' && request()->get('old') && request()->get('new')) {
                    $response = $this->api->actionRename();
                }
                break;

            case 'copy':
                if ($method === 'GET' && request()->get('source') && request()->get('target')) {
                    $response = $this->api->actionCopy();
                }
                break;

            case 'move':
                if ($method === 'GET' && request()->get('old') && request()->get('new')) {
                    $response = $this->api->actionMove();
                }
                break;

            case 'delete':
                if ($method === 'GET' && request()->get('path')) {
                    $response = $this->api->actionDelete();
                }
                break;

            case 'addfolder':
                if ($method === 'GET' && request()->get('path') && request()->get('name')) {
                    $response = $this->api->actionAddFolder();
                }
                break;

            case 'download':
                if ($method === 'GET' && request()->get('path')) {
                    $this->api->actionDownload();
                }
                break;

            case 'getimage':
                if ($method === 'GET' && request()->get('path')) {
                    $thumbnail = isset($_GET['thumbnail']);
                    $this->api->actionGetImage($thumbnail);
                }
                break;

            case 'readfile':
                if (($method === 'GET' || $method === 'HEAD') && request()->get('path')) {
                    $this->api->actionReadFile();
                }
                break;

            case 'summarize':
                if ($method === 'GET') {
                    $response = $this->api->actionSummarize();
                }
                break;

            case 'upload':
                if ($method === 'POST' && request()->get('path')) {
                    $response = $this->api->actionUpload();
                }
                break;

            case 'savefile':
                if ($method === 'POST' && request()->get('path') && request()->get('content')) {
                    $response = $this->api->actionSaveFile();
                }
                break;

            case 'extract':
                if ($method === 'POST' && request()->get('source') && request()->get('target')) {
                    $response = $this->api->actionExtract();
                }
                break;
        }

        if (\is_null($response)) {
            $this->error('INVALID_ACTION');
        }

        echo json_encode([
            'data' => $response,
        ], JSON_THROW_ON_ERROR);
        exit;
    }

    /**
     * Echo error message and terminate the application.
     *
     * @throws \JsonException
     */
    public function error(string $label, array $arguments = [], array $meta = []): void
    {
        $meta['arguments'] = $arguments;
        $message = 'Error code: ' . $label . ', meta: ' . json_encode($meta, JSON_THROW_ON_ERROR);
        logger()->log($message);

        $error_object = [
            'id' => 'server',
            'code' => '500',
            'title' => $label,
            'meta' => $meta,
        ];

        header('Content-Type: application/json');
        header('HTTP/1.1 500 Internal Server Error');

        echo json_encode([
            'errors' => [$error_object],
        ], JSON_THROW_ON_ERROR);

        exit;
    }

    /**
     * Merges two or more arrays into one recursively.
     * If each array has an element with the same string key value, the latter will overwrite the former.
     * Recursive merging will be conducted if both arrays have an element of array type and are having the same key.
     * For array elements which are entirely integer-keyed, latter will straight overwrite the former.
     * For integer-keyed elements, the elements from the latter array will be appended to the former array.
     *
     * @param array $a array to be merged to
     * @param array $b array to be merged from. You can specify additional
     *                 arrays via third argument, fourth argument etc.
     *
     * @return array the merged array (the original arrays are not changed.)
     */
    public function mergeConfigs(array $a, array $b): array
    {
        $args = \func_get_args();
        $res = array_shift($args);
        while (!empty($args)) {
            $next = array_shift($args);
            foreach ($next as $k => $v) {
                if (\is_int($k)) {
                    if (isset($res[$k])) {
                        $res[] = $v;
                    } else {
                        $res[$k] = $v;
                    }
                } elseif (\is_array($v) && isset($res[$k]) && \is_array($res[$k])) {
                    // check if array keys is sequential to consider its as indexed array
                    // http://stackoverflow.com/questions/173400/how-to-check-if-php-array-is-associative-or-sequential
                    if (array_keys($res[$k]) === range(0, \count($res[$k]) - 1)) {
                        $res[$k] = $v;
                    } else {
                        $res[$k] = self::mergeConfigs($res[$k], $v);
                    }
                } else {
                    $res[$k] = $v;
                }
            }
        }

        return $res;
    }

    /**
     * Check if this is running under PHP for Windows.
     */
    public function php_os_is_windows(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }

    /**
     * Get the version number of the application.
     */
    public function version(): string
    {
        return 'RichFilemanager PHP connector v1.2.6';
    }
}
