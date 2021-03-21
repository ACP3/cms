<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

class Filesystem
{
    /**
     * @var array
     */
    private static $excluded = ['.', '..', '.git', '.gitignore', '.svn', '.htaccess', '.htpasswd'];

    public static function scandir(string $directory, array $excluded = []): array
    {
        $scannedDirectory = @\scandir($directory);

        if ($scannedDirectory !== false) {
            $filesAndDirectories = \array_diff(
                $scannedDirectory,
                \array_merge(static::$excluded, $excluded)
            );

            return \array_values($filesAndDirectories);
        }

        return [];
    }
}
