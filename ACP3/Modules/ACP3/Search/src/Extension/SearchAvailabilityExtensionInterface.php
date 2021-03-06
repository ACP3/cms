<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Extension;

interface SearchAvailabilityExtensionInterface
{
    /**
     * @return string
     */
    public function getModuleName();

    /**
     * @param string $searchTerm
     * @param string $areas
     * @param string $sortDirection
     *
     * @return array
     */
    public function fetchSearchResults($searchTerm, $areas, $sortDirection);
}
