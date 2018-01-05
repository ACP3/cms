<?php
namespace ACP3\Core;

class Filesystem
{
    /**
     * @var array
     */
    protected static $excluded = ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd'];

    public static function scandir(string $directory, array $excluded = []): array
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
