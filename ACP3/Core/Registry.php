<?php
namespace ACP3\Core;

/**
 * Class Registry
 *
 * @author Tino Goratsch
 */
abstract class Registry
{
    private static $registry = array();

    public static function set($key, $value)
    {
        if (isset(self::$registry[$key]) === false) {
            self::$registry[$key] = $value;
        }
    }

    public static function get($class)
    {
        return self::$registry[$class];
    }

    public static function remove($class)
    {
        if (isset(self::$registry[$class])) {
            unset(self::$registry[$class]);
        }
    }

    public static function getAll()
    {
        return self::$registry;
    }
}