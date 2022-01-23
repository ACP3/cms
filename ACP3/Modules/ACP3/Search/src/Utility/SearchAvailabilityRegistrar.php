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
     * @var array<string, SearchAvailabilityExtensionInterface>
     */
    protected array $availableModules = [];

    /**
     * @return static
     */
    public function registerModule(SearchAvailabilityExtensionInterface $searchAvailability): self
    {
        $this->availableModules[$searchAvailability->getModuleName()] = $searchAvailability;

        return $this;
    }

    /**
     * @return array<string, SearchAvailabilityExtensionInterface>
     */
    public function getAvailableModules(): array
    {
        return $this->availableModules;
    }

    /**
     * @return string[]
     */
    public function getAvailableModuleNames(): array
    {
        return array_keys($this->availableModules);
    }
}
