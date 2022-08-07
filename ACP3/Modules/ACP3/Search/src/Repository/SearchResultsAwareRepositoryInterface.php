<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Repository;

use ACP3\Modules\ACP3\Search\Enum\SearchAreaEnum;
use ACP3\Modules\ACP3\Search\Enum\SortDirectionEnum;

interface SearchResultsAwareRepositoryInterface
{
    /**
     * @return array<string, mixed>[]
     */
    public function getAllSearchResults(SearchAreaEnum $area, string $searchTerm, SortDirectionEnum $sortDirection): array;
}
