<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Repository;

use ACP3\Core\DataGrid\Repository\AbstractDataGridRepository;

class AccountDataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = AccountRepository::TABLE_NAME;
}
