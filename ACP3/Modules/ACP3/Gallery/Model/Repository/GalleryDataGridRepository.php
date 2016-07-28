<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Model\Repository;


use ACP3\Core\Helpers\DataGrid\ColumnPriorityQueue;
use ACP3\Core\Model\DataGridRepository;
use Doctrine\DBAL\Query\QueryBuilder;

/**
 * Class GalleryDataGridRepository
 * @package ACP3\Modules\ACP3\Gallery\Model\Repository
 */
class GalleryDataGridRepository extends DataGridRepository
{
    const TABLE_NAME = GalleryRepository::TABLE_NAME;

    /**
     * @inheritdoc
     */
    protected function getColumns(ColumnPriorityQueue $gridColumns)
    {
        return [
            'main.id',
            'main.start',
            'main.end',
            'main.title',
            'COUNT(p.gallery_id) AS picture'
        ];
    }

    /**
     * @inheritdoc
     */
    protected function addJoin(QueryBuilder $queryBuilder)
    {
        $queryBuilder->leftJoin(
            'main',
            $this->getTableName(PictureRepository::TABLE_NAME),
            'p',
            'main.id = p.gallery_id'
        );
    }

    /**
     * @inheritdoc
     */
    protected function addGroupBy(QueryBuilder $queryBuilder)
    {
        $queryBuilder->addGroupBy('main.id');
    }

    /**
     * @inheritdoc
     */
    protected function setOrderBy(ColumnPriorityQueue $gridColumns, QueryBuilder $queryBuilder)
    {
        $queryBuilder
            ->addOrderBy('main.start', 'DESC')
            ->addOrderBy('main.end', 'DESC')
            ->addOrderBy('main.id', 'DESC');
    }
}
