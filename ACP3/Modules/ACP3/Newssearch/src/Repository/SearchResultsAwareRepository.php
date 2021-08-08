<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newssearch\Repository;

use ACP3\Core\Database\Connection;
use ACP3\Core\Date;
use ACP3\Core\Repository\AbstractRepository;
use ACP3\Modules\ACP3\Search\Repository\SearchResultsAwareRepositoryInterface;

class SearchResultsAwareRepository extends AbstractRepository implements SearchResultsAwareRepositoryInterface
{
    use \ACP3\Core\Repository\PublicationPeriodAwareTrait;

    public const TABLE_NAME = \ACP3\Modules\ACP3\News\Repository\NewsRepository::TABLE_NAME;

    /**
     * @var Date
     */
    protected $date;

    /**
     * SearchResultsAwareRepository constructor.
     */
    public function __construct(Connection $db, Date $date)
    {
        parent::__construct($db);

        $this->date = $date;
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
            "SELECT `id`, `title`, `text` FROM {$this->getTableName()} WHERE MATCH ({$fields}) AGAINST ({$this->db->getConnection()->quote($searchTerm)} IN BOOLEAN MODE) AND {$this->getPublicationPeriod()} AND `active` = :active ORDER BY `start` {$sortDirection}, `end` {$sortDirection}, `id` {$sortDirection}",
            ['time' => $this->date->getCurrentDateTime(), 'active' => 1]
        );
    }
}
