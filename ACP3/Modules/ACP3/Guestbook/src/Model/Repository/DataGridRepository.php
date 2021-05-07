<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Model\Repository;

use ACP3\Core\DataGrid\Model\Repository\AbstractDataGridRepository;

class DataGridRepository extends AbstractDataGridRepository
{
    public const TABLE_NAME = GuestbookRepository::TABLE_NAME;
}
