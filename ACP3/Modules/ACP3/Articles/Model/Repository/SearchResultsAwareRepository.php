<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model\Repository;

use ACP3\Core\Database\Connection;
use ACP3\Core\Date;
use ACP3\Core\Model\Repository\AbstractRepository;
use ACP3\Core\Model\Repository\PublicationPeriodAwareTrait;
use ACP3\Modules\ACP3\Search\Model\Repository\SearchResultsAwareRepositoryInterface;

class SearchResultsAwareRepository extends AbstractRepository implements SearchResultsAwareRepositoryInterface
{
    const TABLE_NAME = ArticleRepository::TABLE_NAME;

    use PublicationPeriodAwareTrait;

    /**
     * @var Date
     */
    protected $date;

    /**
     * SearchResultsAwareRepository constructor.
     * @param Connection $db
     * @param Date $date
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
            "SELECT `id`, `title`, `text` FROM {$this->getTableName()} WHERE MATCH ({$fields}) AGAINST ({$this->db->getConnection()->quote($searchTerm)} IN BOOLEAN MODE) AND {$this->getPublicationPeriod()} AND `active` = 1 ORDER BY `start` {$sortDirection}, `end` {$sortDirection}, `title` {$sortDirection}",
            ['time' => $this->date->getCurrentDateTime()]
        );
    }
}
