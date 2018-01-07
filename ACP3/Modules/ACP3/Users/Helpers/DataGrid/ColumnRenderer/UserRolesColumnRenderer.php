<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Helpers\DataGrid\ColumnRenderer;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;

class UserRolesColumnRenderer extends AbstractColumnRenderer
{
    /**
     * @var ACLInterface
     */
    protected $acl;

    /**
     * UserRolesColumnRenderer constructor.
     *
     * @param ACLInterface $acl
     */
    public function __construct(ACLInterface $acl)
    {
        $this->acl = $acl;
    }

    /**
     * {@inheritdoc}
     */
    protected function getDbValueIfExists(array $dbResultRow, $field)
    {
        return isset($dbResultRow[$field]) ? \implode(', ', $this->acl->getUserRoleNames($dbResultRow[$field])) : null;
    }
}
