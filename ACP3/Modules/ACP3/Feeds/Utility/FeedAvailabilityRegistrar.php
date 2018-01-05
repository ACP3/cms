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
    protected $availableModules = [];

    /**
     * @param FeedAvailabilityExtensionInterface $searchAvailability
     * @return $this
     */
    public function registerModule(FeedAvailabilityExtensionInterface $searchAvailability)
    {
        $this->availableModules[$searchAvailability->getModuleName()] = $searchAvailability;

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableModuleNames()
    {
        return \array_keys($this->availableModules);
    }

    /**
     * @param string $moduleName
     * @return FeedAvailabilityExtensionInterface
     */
    public function getFeedItemsByModuleName($moduleName)
    {
        if (isset($this->availableModules[$moduleName])) {
            return $this->availableModules[$moduleName];
        }

        throw new \InvalidArgumentException('There are no available feeds items for the requested module "' . $moduleName . '".');
    }
}
