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
    /**
     * @var \ACP3\Core\ACL
     */
    private $acl;

    public function __construct(ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, $field): ?string
    {
        return \array_key_exists($field, $dbResultRow) ? \implode(', ', $this->acl->getUserRoleNames($dbResultRow[$field])) : null;
    }
}
