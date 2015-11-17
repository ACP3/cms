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
    public function fetchDataAndRenderColumn(array $column, array $dbResultRow, $identifier, $primaryKey)
    {
        $value = $this->getValue($column, $dbResultRow);

        return $this->render($column, implode(', ', $this->acl->getUserRoleNames($value)));
    }

    /**
     * @return string
     */
    public function getType()
    {
        return 'user_roles';
    }
}