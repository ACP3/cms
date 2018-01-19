<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Modules\Installer\MigrationInterface;

class MigrationRegistrar
{
    /**
     * @var MigrationInterface[]
     */
    private $migrations = [];

    /**
     * @param string             $serviceId
     * @param MigrationInterface $schema
     */
    public function set($serviceId, MigrationInterface $schema)
    {
        $this->migrations[$serviceId] = $schema;
    }

    /**
     * @return MigrationInterface[]
     */
    public function all()
    {
        return $this->migrations;
    }

    /**
     * @param string $serviceId
     *
     * @return bool
     */
    public function has($serviceId)
    {
        return isset($this->migrations[$serviceId]);
    }

    /**
     * @param string $serviceId
     *
     * @return MigrationInterface
     *
     * @throws \InvalidArgumentException
     */
    public function get($serviceId)
    {
        if ($this->has($serviceId)) {
            return $this->migrations[$serviceId];
        }

        throw new \InvalidArgumentException(
            \sprintf('The migration with the service id "%s" could not be found.', $serviceId)
        );
    }
}
