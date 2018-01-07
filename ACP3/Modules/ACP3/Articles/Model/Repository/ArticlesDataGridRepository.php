<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Model\Repository;

use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;

class ArticlesDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = ArticlesRepository::TABLE_NAME;
}