<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Model\Repository;

use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;
use ACP3\Core\Helpers\DataGrid\QueryOption;
use ACP3\Modules\ACP3\Newsletter\Helper\AccountStatus;
use Doctrine\DBAL\Query\QueryBuilder;

class NewsletterAccountsDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = NewsletterAccountsRepository::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected function addWhere(QueryBuilder $queryBuilder, QueryOption ...$queryOptions)
    {
        parent::addWhere($queryBuilder, ...$queryOptions);

        $queryBuilder->where('`main`.`status` != :status');
    }

    /**
     * @inheritdoc
     */
    protected function getParameters(QueryOption ...$queryOptions)
    {
        return array_merge(
            ['status' => AccountStatus::ACCOUNT_STATUS_DISABLED],
            parent::getParameters(...$queryOptions)
        );
    }
}
