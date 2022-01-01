<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Shariff\Backend;

class ServiceFactory
{
    /** @var ServiceInterface[] */
    private array $serviceMap = [];

    public function registerService(string $name, ServiceInterface $service): void
    {
        $this->serviceMap[$name] = $service;
    }

    /**
     * @param string[] $serviceNames
     */
    public function getServicesByName(array $serviceNames, array $config): array
    {
        $services = [];
        foreach ($serviceNames as $serviceName) {
            try {
                $service = $this->createService($serviceName, $config);
            } catch (\InvalidArgumentException) {
                continue;
            }
            $services[] = $service;
        }

        return $services;
    }

    private function createService(string $serviceName, array $config): ServiceInterface
    {
        if (isset($this->serviceMap[$serviceName])) {
            $service = $this->serviceMap[$serviceName];
        } else {
            $serviceClass = '\\ACP3\\Modules\\ACP3\\Share\\Shariff\\Backend\\' . $serviceName;
            if (!class_exists($serviceClass)) {
                throw new \InvalidArgumentException('Invalid service name "' . $serviceName . '".');
            }
            $service = new $serviceClass();
        }

        if (isset($config[$serviceName])) {
            $service->setConfig($config[$serviceName]);
        }

        return $service;
    }
}
