<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Installer;

use ACP3\Core\Modules\Installer\SampleDataInterface;

class SampleDataRegistrar
{
    /**
     * @var SampleDataInterface[]
     */
    private $sampleData = [];

    /**
     * @param string $serviceId
     * @param SampleDataInterface $sampleData
     */
    public function set($serviceId, SampleDataInterface $sampleData)
    {
        $this->sampleData[$serviceId] = $sampleData;
    }

    /**
     * @return SampleDataInterface[]
     */
    public function all()
    {
        return $this->sampleData;
    }

    /**
     * @param string $serviceId
     * @return bool
     */
    public function has($serviceId)
    {
        return isset($this->sampleData[$serviceId]);
    }

    /**
     * @param string $serviceId
     * @return SampleDataInterface
     * @throws \InvalidArgumentException
     */
    public function get($serviceId)
    {
        if ($this->has($serviceId)) {
            return $this->sampleData[$serviceId];
        }

        throw new \InvalidArgumentException(
            sprintf('The sample data with the service id "%s" could not be found.', $serviceId)
        );
    }
}
