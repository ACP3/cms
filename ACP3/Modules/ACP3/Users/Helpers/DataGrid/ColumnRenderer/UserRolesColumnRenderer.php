<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;

class UserRolesColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;

    /**
     * UserRolesColumnRenderer constructor.
     *
     * @param \ACP3\Core\ACL $acl
     */
    public function __construct(ACL $acl)
    {
        $this->acl = $acl;
    }

    /**
     * @inheritdoc
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? \implode(', ', $this->acl->getUserRoleNames($dbResultRow[$field])) : null;
    }
}
