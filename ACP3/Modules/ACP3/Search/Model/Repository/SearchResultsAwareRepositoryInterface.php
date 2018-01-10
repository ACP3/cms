<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\Model\Repository;

interface SearchResultsAwareRepositoryInterface
{
    /**
     * @param string $fields
     * @param string $searchTerm
     * @param string $sortDirection
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function getAllSearchResults(string $fields, string $searchTerm, string $sortDirection);
}
