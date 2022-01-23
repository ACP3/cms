<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Repository;

interface SearchResultsAwareRepositoryInterface
{
    /**
     * @return array<string, mixed>[]
     */
    public function getAllSearchResults(string $fields, string $searchTerm, string $sortDirection): array;
}
