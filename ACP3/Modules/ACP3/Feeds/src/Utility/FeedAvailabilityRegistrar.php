<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Utility;

use ACP3\Modules\ACP3\Feeds\Extension\FeedAvailabilityExtensionInterface;

class FeedAvailabilityRegistrar
{
    /**
     * @var FeedAvailabilityExtensionInterface[]
     */
    private array $availableModules = [];

    /**
     * @return static
     */
    public function registerModule(FeedAvailabilityExtensionInterface $searchAvailability): self
    {
        $this->availableModules[$searchAvailability->getModuleName()] = $searchAvailability;

        return $this;
    }

    /**
     * @return string[]
     */
    public function getAvailableModuleNames(): array
    {
        return array_keys($this->availableModules);
    }

    public function getFeedItemsByModuleName(string $moduleName): FeedAvailabilityExtensionInterface
    {
        if (isset($this->availableModules[$moduleName])) {
            return $this->availableModules[$moduleName];
        }

        throw new \InvalidArgumentException('There are no available feeds items for the requested module "' . $moduleName . '".');
    }
}
