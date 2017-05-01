<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Permissions\View\Block\Admin;


use ACP3\Core\ACL;
use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;

class RolesListingBlock extends AbstractBlock
{
    /**
     * @var ACL
     */
    private $acl;

    /**
     * RolesListingBlock constructor.
     * @param BlockContext $context
     * @param ACL $acl
     */
    public function __construct(BlockContext $context, ACL $acl)
    {
        parent::__construct($context);

        $this->acl = $acl;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $roles = $this->acl->getAllRoles();
        $cRoles = count($roles);

        for ($i = 0; $i < $cRoles; ++$i) {
            $roles[$i]['spaces'] = str_repeat('&nbsp;&nbsp;', $roles[$i]['level']);
        }

        return [
            'roles' => $roles,
            'can_delete' => $this->acl->hasPermission('admin/permissions/index/delete'),
            'can_edit' => $this->acl->hasPermission('admin/permissions/index/edit'),
            'can_order' => $this->acl->hasPermission('admin/permissions/index/order')
        ];
    }
}
