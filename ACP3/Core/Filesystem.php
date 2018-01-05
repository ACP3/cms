<?php
namespace ACP3\Core;

class Filesystem
{
    /**
     * @var array
     */
    protected static $excluded = ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd'];

    /**
     * @param string $directory
     * @param array  $excluded
     *
     * @return array
     */
    public static function scandir($directory, array $excluded = [])
    {
        $directory = @scandir($directory);

        if ($directory !== false) {
            $filesAndDirectories = array_diff(
                $directory,
                array_merge(static::$excluded, $excluded)
            );

            return array_values($filesAndDirectories);
        }

        return [];
    }
}
