<?php
/**
 * Copyright (c) 2017 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Installer;


use ACP3\Core\Modules\Installer\SchemaInterface;

class SchemaRegistrar
{
    /**
     * @var SchemaInterface[]
     */
    private $schemas = [];

    /**
     * @param string $serviceId
     * @param SchemaInterface $schema
     */
    public function set($serviceId, SchemaInterface $schema)
    {
        $this->schemas[$serviceId] = $schema;
    }

    /**
     * @return SchemaInterface[]
     */
    public function all()
    {
        return $this->schemas;
    }

    /**
     * @param string $serviceId
     * @return bool
     */
    public function has($serviceId)
    {
        return isset($this->schemas[$serviceId]);
    }

    /**
     * @param string $serviceId
     * @return SchemaInterface
     * @throws \InvalidArgumentException
     */
    public function get($serviceId)
    {
        if ($this->has($serviceId)) {
            return $this->schemas[$serviceId];
        }

        throw new \InvalidArgumentException(
            sprintf('The schema with the service id "%s" could not be found.', $serviceId)
        );
    }
}
