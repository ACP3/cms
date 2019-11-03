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
    protected static $excluded = ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd'];

    /**
     * @param string $directory
     *
     * @return array
     */
    public static function scandir($directory, array $excluded = [])
    {
        $directory = @\scandir($directory);

        if ($directory !== false) {
            $filesAndDirectories = \array_diff(
                $directory,
                \array_merge(static::$excluded, $excluded)
            );

            return \array_values($filesAndDirectories);
        }

        return [];
    }
}
