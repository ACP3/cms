<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Modules\Installer\SchemaInterface;
use Psr\Container\ContainerInterface;

class SchemaRegistrar implements ContainerInterface
{
    /**
     * @var Array<string, SchemaInterface>
     */
    private $schemas = [];

    public function set(SchemaInterface $schema)
    {
        $this->schemas[$schema->getModuleName()] = $schema;
    }

    /**
     * @return Array<string, SchemaInterface>
     */
    public function all()
    {
        return $this->schemas;
    }

    /**
     * @param string $moduleName
     *
     * @return bool
     */
    public function has($moduleName)
    {
        return isset($this->schemas[$moduleName]);
    }

    /**
     * @param string $moduleName
     *
     * @return \ACP3\Core\Modules\Installer\SchemaInterface
     */
    public function get($moduleName)
    {
        if ($this->has($moduleName)) {
            return $this->schemas[$moduleName];
        }

        throw new \InvalidArgumentException(sprintf('The schema with the service id "%s" could not be found.', $moduleName));
    }
}
