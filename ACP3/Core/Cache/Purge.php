<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Cache;


use ACP3\Core\Filesystem;

/**
 * Class Purge
 * @package ACP3\Core\Cache
 */
class Purge
{
    /**
     * @param string|array $directory
     * @param string       $cacheId
     *
     * @return bool
     */
    public static function doPurge($directory, $cacheId = '')
    {
        if (is_array($directory)) {
            return self::handleMultipleDirectories($directory, $cacheId);
        }

        self::purgeCurrentDirectory($directory, $cacheId);

        return true;
    }

    /**
     * @param array  $directories
     * @param string $cacheId
     *
     * @return bool
     */
    protected static function handleMultipleDirectories(array $directories, $cacheId)
    {
        foreach ($directories as $directory) {
            static::doPurge($directory, $cacheId);
        }

        return true;
    }

    /**
     * @param string $directory
     * @param string $cacheId
     */
    protected static function purgeCurrentDirectory($directory, $cacheId)
    {
        if (is_dir($directory)) {
            foreach (Filesystem::scandir($directory) as $dirContent) {
                $path = "$directory/$dirContent";

                if (is_dir($path)) {
                    static::purgeCurrentDirectory($path, $cacheId);
                } elseif (empty($cacheId) || strpos($dirContent, $cacheId) !== false) {
                    @unlink($path);
                }
            }

            if (empty($cacheId)) {
                @rmdir($directory);
            }
        } elseif (is_file($directory)) {
            @unlink($directory);
        }
    }
}
