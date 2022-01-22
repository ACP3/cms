<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\DataGrid\ColumnRenderer;

use ACP3\Core\ACL;
use ACP3\Core\DataGrid\ColumnRenderer\AbstractColumnRenderer;

class UserRolesColumnRenderer extends AbstractColumnRenderer
{
    public function __construct(private ACL $acl)
    {
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, string $field): ?string
    {
        return !empty($dbResultRow[$field]) ? implode(', ', $this->acl->getUserRoleNames($dbResultRow[$field])) : null;
    }
}
