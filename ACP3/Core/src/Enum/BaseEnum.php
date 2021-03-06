<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Enum;

use ReflectionClass;

class BaseEnum
{
    /**
     * @var array|null
     */
    private static $constCacheArray;

    /**
     * @throws \ReflectionException
     */
    protected static function getConstants(): array
    {
        if (self::$constCacheArray === null) {
            self::$constCacheArray = [];
        }
        $calledClass = static::class;
        if (!\array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }

        return self::$constCacheArray[$calledClass];
    }

    /**
     * @throws \ReflectionException
     */
    public static function isValidName(string $name, bool $strict = false): bool
    {
        $constants = self::getConstants();

        if ($strict) {
            return \array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));

        return \in_array(strtolower($name), $keys, true);
    }

    /**
     * @param mixed $value
     *
     * @throws \ReflectionException
     */
    public static function isValidValue($value, bool $strict = true): bool
    {
        $values = array_values(self::getConstants());

        return \in_array($value, $values, $strict);
    }
}
