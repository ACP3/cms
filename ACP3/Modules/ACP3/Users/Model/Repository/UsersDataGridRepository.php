<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Model\Repository;

use ACP3\Core\Helpers\DataGrid\Model\Repository\AbstractDataGridRepository;

class UsersDataGridRepository extends AbstractDataGridRepository
{
    const TABLE_NAME = UsersRepository::TABLE_NAME;
}
