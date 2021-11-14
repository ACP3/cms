<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Cache;

use ACP3\Core\Filesystem;

class Purge
{
    /**
     * @param string|array $directory
     */
    public static function doPurge($directory, string $cacheIdPrefix = ''): bool
    {
        if (\is_array($directory)) {
            return self::handleMultipleDirectories($directory, $cacheIdPrefix);
        }

        self::purgeCurrentDirectory($directory, $cacheIdPrefix);

        return true;
    }

    private static function handleMultipleDirectories(array $directories, string $cacheIdPrefix): bool
    {
        foreach ($directories as $directory) {
            self::doPurge($directory, $cacheIdPrefix);
        }

        return true;
    }

    private static function purgeCurrentDirectory(string $fileOrDirectory, string $cacheIdPrefix): void
    {
        if (is_link($fileOrDirectory)) {
            self::purgeCurrentDirectory(readlink($fileOrDirectory), $cacheIdPrefix);
        } elseif (is_dir($fileOrDirectory)) {
            foreach (Filesystem::scandir($fileOrDirectory) as $dirContent) {
                $path = "$fileOrDirectory/$dirContent";

                if (is_dir($path)) {
                    self::purgeCurrentDirectory($path, $cacheIdPrefix);
                    if (empty($cacheIdPrefix)) {
                        @rmdir($path);
                    }
                } elseif (empty($cacheIdPrefix) || strpos($dirContent, $cacheIdPrefix) === 0) {
                    @unlink($path);
                }
            }
        } elseif (is_file($fileOrDirectory)) {
            @unlink($fileOrDirectory);
        }
    }
}
