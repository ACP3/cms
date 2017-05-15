<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model\Repository;

/**
 * Class DataGridRepository
 * @package ACP3\Modules\ACP3\Articles\Model\Repository
 */
class DataGridRepository extends \ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository
{
    const TABLE_NAME = ArticleRepository::TABLE_NAME;
}
