<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use Psr\Container\ContainerInterface;

class SchemaRegistrar implements ContainerInterface
{
    /**
     * @var Array<string, SchemaInterface>
     */
    private $schemas = [];

    public function set(SchemaInterface $schema): void
    {
        $this->schemas[$schema->getModuleName()] = $schema;
    }

    /**
     * @return Array<string, SchemaInterface>
     */
    public function all(): array
    {
        return $this->schemas;
    }

    /**
     * {@inheritDoc}
     */
    public function has(string $moduleName): bool
    {
        return isset($this->schemas[$moduleName]);
    }

    /**
     * @return \ACP3\Core\Installer\SchemaInterface
     */
    public function get(string $moduleName)
    {
        if ($this->has($moduleName)) {
            return $this->schemas[$moduleName];
        }

        throw new \InvalidArgumentException(sprintf('The schema with the service id "%s" could not be found.', $moduleName));
    }
}
