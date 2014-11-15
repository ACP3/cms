<?php
namespace ACP3\Core;

/**
 * Class Registry
 *
 * @author Tino Goratsch
 */
class Registry
{
    protected static $registry = [];

    /**
     * Inject an object to the registry
     *
     * @param $key
     * @param $value
     */
    public static function set($key, $value)
    {
        if (isset(self::$registry[$key]) === false) {
            self::$registry[$key] = $value;
        }
    }

    /**
     * Get an object from the registry
     *
     * @param $class
     *
     * @return mixed
     */
    public static function get($class)
    {
        return self::$registry[$class];
    }

    /**
     * Remove an object from the registry
     *
     * @param $class
     */
    public static function remove($class)
    {
        if (isset(self::$registry[$class])) {
            unset(self::$registry[$class]);
        }
    }

    /**
     * Get all registered object inside the registry
     *
     * @return array
     */
    public static function getAll()
    {
        return self::$registry;
    }
}