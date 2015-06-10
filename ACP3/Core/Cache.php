<?php
namespace ACP3\Core;

use Symfony\Component\DependencyInjection\Container;

/**
 * Class Cache
 * @package ACP3\Core
 */
class Cache
{
    /**
     * @var \Symfony\Component\DependencyInjection\Container
     */
    protected $container;
    /**
     * @var string
     */
    protected $namespace = '';
    /**
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    protected $driver;
    /**
     * @var array
     */
    protected $retrievedCacheData = [];

    /**
     * @param \Symfony\Component\DependencyInjection\Container $container
     * @param                                                  $namespace
     * @throws \InvalidArgumentException
     */
    public function __construct(
        Container $container,
        $namespace
    ) {
        $this->container = $container;
        $this->namespace = $namespace;
    }

    /**
     * @param string $dir
     * @param string $cacheId
     *
     * @return bool
     */
    public static function purge($dir, $cacheId = '')
    {
        if (is_file($dir) === true) {
            return @unlink($dir);
        }

        $files = array_diff(scandir($dir), ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd']);
        foreach ($files as $file) {
            $path = "$dir/$file";

            if (is_dir($path)) {
                static::purge($path, $cacheId);
                if (empty($cacheId)) {
                    @rmdir($path);
                }
            } else {
                if (!empty($cacheId) && strpos($file, $cacheId) === false) {
                    continue;
                }

                @unlink($path);
            }
        }

        if (!empty($cacheId)) {
            return true;
        }

        return true;
    }

    /**
     * @param $id
     *
     * @return bool|mixed|string
     */
    public function fetch($id)
    {
        if (isset($this->retrievedCacheData[$id]) === false) {
            $this->retrievedCacheData[$id] = $this->getDriver()->fetch($id);
        }

        return $this->retrievedCacheData[$id];
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function contains($id)
    {
        return isset($this->retrievedCacheData[$id]) === true || $this->getDriver()->contains($id);
    }

    /**
     * @param     $id
     * @param     $data
     * @param int $lifetime
     *
     * @return bool
     */
    public function save($id, $data, $lifetime = 0)
    {
        $this->retrievedCacheData[$id] = $data;

        return $this->getDriver()->save($id, $data, $lifetime);
    }

    /**
     * @param $id
     *
     * @return bool
     */
    public function delete($id)
    {
        unset($this->retrievedCacheData[$id]);

        return $this->getDriver()->delete($id);
    }

    /**
     * @return bool
     */
    public function deleteAll()
    {
        return $this->getDriver()->deleteAll();
    }

    /**
     * @return bool
     */
    public function flushAll()
    {
        return $this->getDriver()->flushAll();
    }

    /**
     * @return \Doctrine\Common\Cache\CacheProvider
     */
    public function getDriver()
    {
        // Init the cache driver
        if ($this->driver === null) {
            if ($this->container->hasParameter('cache_driver')) {
                $driverName = $this->container->getParameter('cache_driver');
            } else {
                $driverName = 'Array';
            }

            // If debug mode is enabled, override the cache driver configuration
            if (defined('DEBUG') && DEBUG === true) {
                $driverName = 'Array';
            }

            $cacheDriverPath = "\\Doctrine\\Common\\Cache\\" . $driverName . 'Cache';
            if (class_exists($cacheDriverPath)) {
                if ($driverName === 'PhpFile') {
                    $cacheDir = CACHE_DIR . 'sql/';
                    $this->driver = new $cacheDriverPath($cacheDir);
                } else {
                    $this->driver = new $cacheDriverPath();
                }

                $this->driver->setNamespace($this->namespace);
            } else {
                throw new \InvalidArgumentException(sprintf('Could not find the requested cache driver "%s"!', $cacheDriverPath));
            }
        }

        return $this->driver;
    }
}
