<?php
namespace ACP3\Core;

/**
 * Class Filesystem
 * @package ACP3\Core
 */
class Filesystem
{
    /**
     * @var array
     */
    protected static $excluded = ['.', '..', '.gitignore', '.svn', '.htaccess', '.htpasswd'];

    /**
     * @param       $directory
     * @param array $excluded
     *
     * @return array
     */
    public static function scandir($directory, array $excluded = [])
    {
        return array_diff(
            scandir($directory),
            array_merge(static::$excluded, $excluded)
        );
    }
}