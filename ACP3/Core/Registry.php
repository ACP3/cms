<?php
namespace ACP3\Core;

class Registry
{
    private static $registry = [];

    /**
     * Inject an object to the registry
     *
     * @param string $key
     * @param mixed $value
     */
    public function set($key, $value)
    {
        if (isset(self::$registry[$key]) === false) {
            self::$registry[$key] = $value;
        }
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return isset(self::$registry[$key]);
    }

    /**
     * Get an object from the registry
     *
     * @param string $key
     * @param null $default
     * @return mixed
     */
    public function get($key, $default = null)
    {
        return $this->has($key) ? self::$registry[$key] : $default;
    }

    /**
     * Remove an object from the registry
     *
     * @param string $key
     */
    public function remove($key)
    {
        if (isset(self::$registry[$key])) {
            unset(self::$registry[$key]);
        }
    }
}
