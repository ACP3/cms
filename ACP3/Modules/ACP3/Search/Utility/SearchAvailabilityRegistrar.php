<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Search\Utility;


class SearchAvailabilityRegistrar
{
    /**
     * @var SearchAvailabilityInterface[]
     */
    protected $availableModules = [];

    /**
     * @param SearchAvailabilityInterface $searchAvailability
     * @return $this
     */
    public function registerModule(SearchAvailabilityInterface $searchAvailability)
    {
        $this->availableModules[$searchAvailability->getModuleName()] = $searchAvailability;

        return $this;
    }

    /**
     * @return SearchAvailabilityInterface[]
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
        return array_keys($this->availableModules);
    }
}
