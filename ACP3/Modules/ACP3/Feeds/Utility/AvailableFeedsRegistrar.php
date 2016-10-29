<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Feeds\Utility;


class AvailableFeedsRegistrar
{
    /**
     * @var FeedAvailabilityInterface[]
     */
    protected $availableModules = [];

    /**
     * @param FeedAvailabilityInterface $searchAvailability
     * @return $this
     */
    public function registerModule(FeedAvailabilityInterface $searchAvailability)
    {
        $this->availableModules[$searchAvailability->getModuleName()] = $searchAvailability;

        return $this;
    }

    /**
     * @return array
     */
    public function getAvailableModuleNames()
    {
        return array_keys($this->availableModules);
    }

    /**
     * @param string $moduleName
     * @return FeedAvailabilityInterface
     */
    public function getFeedItemsByModuleName($moduleName)
    {
        if (isset($this->availableModules[$moduleName])) {
            return $this->availableModules[$moduleName];
        }

        throw new \InvalidArgumentException('There are no available feeds items for the requested module "' . $moduleName . '".');
    }
}
