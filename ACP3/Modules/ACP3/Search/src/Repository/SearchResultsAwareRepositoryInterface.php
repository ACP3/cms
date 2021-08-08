<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Repository;

interface SearchResultsAwareRepositoryInterface
{
    /**
     * @param string $fields
     * @param string $searchTerm
     * @param string $sortDirection
     *
     * @return array
     */
    public function getAllSearchResults($fields, $searchTerm, $sortDirection);
}
