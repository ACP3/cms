<?php
namespace ACP3\Core;

/**
 * Class Registry
 * @package ACP3\Core
 */
class Registry
{
    protected static $registry = [];

    /**
     * Inject an object to the registry
     *
     * @param string $key
     * @param mixed $value
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
     * @param string $key
     *
     * @return mixed
     */
    public static function get($key)
    {
        return self::$registry[$key];
    }

    /**
     * Remove an object from the registry
     *
     * @param string $key
     */
    public static function remove($key)
    {
        if (isset(self::$registry[$key])) {
            unset(self::$registry[$key]);
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
