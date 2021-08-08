<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Repository;

class DataGridRepository extends \ACP3\Core\DataGrid\Repository\AbstractDataGridRepository
{
    public const TABLE_NAME = GuestbookRepository::TABLE_NAME;
}
