<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Model\Repository;

use ACP3\Core\Model\Repository\AbstractRepository;

class CommentByModuleRepository extends AbstractRepository
{
    public const TABLE_NAME = 'comments';
    public const PRIMARY_KEY_COLUMN = 'module_id';
}
