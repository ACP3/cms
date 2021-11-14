<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articlessearch\Repository;

use ACP3\Core\Database\Connection;
use ACP3\Core\Date;
use ACP3\Core\Repository\PublicationPeriodAwareTrait;
use ACP3\Modules\ACP3\Articles\Repository\ArticleRepository;
use ACP3\Modules\ACP3\Search\Repository\SearchResultsAwareRepositoryInterface;

class SearchResultsAwareRepository extends \ACP3\Core\Repository\AbstractRepository implements SearchResultsAwareRepositoryInterface
{
    use PublicationPeriodAwareTrait;
    public const TABLE_NAME = ArticleRepository::TABLE_NAME;

    public function __construct(Connection $db, private Date $date)
    {
        parent::__construct($db);
    }

    /**
     * @param string $fields
     * @param string $searchTerm
     * @param string $sortDirection
     *
     * @return array
     */
    public function getAllSearchResults($fields, $searchTerm, $sortDirection)
    {
        return $this->db->fetchAll(
            "SELECT `id`, `title`, `text` FROM {$this->getTableName()} WHERE MATCH ({$fields}) AGAINST ({$this->db->getConnection()->quote($searchTerm)} IN BOOLEAN MODE) AND {$this->getPublicationPeriod()} AND `active` = 1 ORDER BY `start` {$sortDirection}, `end` {$sortDirection}, `title` {$sortDirection}",
            ['time' => $this->date->getCurrentDateTime()]
        );
    }
}
