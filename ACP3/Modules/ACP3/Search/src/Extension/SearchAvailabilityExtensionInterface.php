<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Extension;

use ACP3\Modules\ACP3\Search\Enum\SearchAreaEnum;
use ACP3\Modules\ACP3\Search\Enum\SortDirectionEnum;

interface SearchAvailabilityExtensionInterface
{
    public function getModuleName(): string;

    /**
     * @return array<string, mixed>[]
     */
    public function fetchSearchResults(string $searchTerm, SearchAreaEnum $areas, SortDirectionEnum $sortDirection): array;
}
