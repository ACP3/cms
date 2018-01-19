<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Utility;

use ACP3\Modules\ACP3\Search\Extension\SearchAvailabilityExtensionInterface;

class SearchAvailabilityRegistrar
{
    /**
     * @var SearchAvailabilityExtensionInterface[]
     */
    protected $availableModules = [];

    /**
     * @param SearchAvailabilityExtensionInterface $searchAvailability
     *
     * @return $this
     */
    public function registerModule(SearchAvailabilityExtensionInterface $searchAvailability)
    {
        $this->availableModules[$searchAvailability->getModuleName()] = $searchAvailability;

        return $this;
    }

    /**
     * @return SearchAvailabilityExtensionInterface[]
     */
    public function getAvailableModules()
    {
        return $this->availableModules;
    }

    /**
     * @return array
     */
    public function getAvailableModuleNames()
    {
        return \array_keys($this->availableModules);
    }
}
