<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff;

use ACP3\Modules\ACP3\Share\Shariff\Backend\ServiceInterface;

class SocialSharingBackendServiceLocator
{
    /** @var ServiceInterface[] */
    private array $serviceMap = [];

    public function registerService(ServiceInterface $service): void
    {
        $this->serviceMap[strtolower($service->getName())] = $service;
    }

    /**
     * @return ServiceInterface[]
     */
    public function getServices(): array
    {
        return $this->serviceMap;
    }

    /**
     * @param string[]             $serviceNames
     * @param array<string, mixed> $config
     *
     * @return ServiceInterface[]
     */
    public function getServicesByName(array $serviceNames, array $config): array
    {
        $services = [];
        foreach ($serviceNames as $serviceName) {
            try {
                $services[$serviceName] = $this->getServiceByName($serviceName, $config);
            } catch (\InvalidArgumentException) {
                continue;
            }
        }

        return $services;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function getServiceByName(string $serviceName, array $config): ServiceInterface
    {
        $serviceName = strtolower($serviceName);

        if (isset($this->serviceMap[$serviceName])) {
            $service = $this->serviceMap[$serviceName];
        } else {
            throw new \InvalidArgumentException('Invalid service name "' . $serviceName . '".');
        }

        if (isset($config[$serviceName])) {
            $service->setConfig($config[$serviceName]);
        }

        return $service;
    }
}
