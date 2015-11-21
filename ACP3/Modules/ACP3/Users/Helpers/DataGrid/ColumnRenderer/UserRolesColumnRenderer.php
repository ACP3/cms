<?php
namespace ACP3\Modules\ACP3\Users\Helpers\DataGrid\ColumnRenderer;


use ACP3\Core\ACL;
use ACP3\Core\Helpers\DataGrid\ColumnRenderer\AbstractColumnRenderer;

/**
 * Class UserRolesColumnRenderer
 * @package ACP3\Modules\ACP3\Users\Helpers\DataGrid\ColumnRenderer
 */
class UserRolesColumnRenderer extends AbstractColumnRenderer
{
    const NAME = 'user_roles';

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
        return isset($dbResultRow[$field]) ? implode(', ', $this->acl->getUserRoleNames($dbResultRow[$field])) : null;
    }
}