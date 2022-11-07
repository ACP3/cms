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
     * @param string[]|string $directory
     */
    public static function doPurge(array|string $directory, string $cacheIdPrefix = ''): bool
    {
        if (\is_array($directory)) {
            return self::handleMultipleDirectories($directory, $cacheIdPrefix);
        }

        self::purgeCurrentDirectory($directory, $cacheIdPrefix);

        return true;
    }

    /**
     * @param string[] $directories
     */
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
            if (false !== ($link = readlink($fileOrDirectory))) {
                self::purgeCurrentDirectory($link, $cacheIdPrefix);
            }
        } elseif (is_dir($fileOrDirectory)) {
            foreach (Filesystem::scandir($fileOrDirectory) as $dirContent) {
                $path = "$fileOrDirectory/$dirContent";

                if (is_dir($path)) {
                    self::purgeCurrentDirectory($path, $cacheIdPrefix);
                    if (empty($cacheIdPrefix)) {
                        @rmdir($path);
                    }
                } elseif (empty($cacheIdPrefix) || str_starts_with($dirContent, $cacheIdPrefix)) {
                    @unlink($path);
                }
            }
        } elseif (is_file($fileOrDirectory)) {
            @unlink($fileOrDirectory);
        }
    }
}
