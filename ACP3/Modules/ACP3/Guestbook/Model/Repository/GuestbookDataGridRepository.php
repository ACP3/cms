<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Guestbook\Model\Repository;

use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;

class GuestbookDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = GuestbookRepository::TABLE_NAME;
}
