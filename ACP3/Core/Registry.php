<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core;

class Registry
{
    private static $registry = [];

    /**
     * Inject an object to the registry.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, $value): void
    {
        if (isset(self::$registry[$key]) === false) {
            self::$registry[$key] = $value;
        }
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset(self::$registry[$key]);
    }

    /**
     * Get an object from the registry.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        return $this->has($key) ? self::$registry[$key] : $default;
    }

    /**
     * Remove an object from the registry.
     *
     * @param string $key
     */
    public function remove(string $key): void
    {
        if (isset(self::$registry[$key])) {
            unset(self::$registry[$key]);
        }
    }
}
