<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Create extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\ViewProviders\AdminRoleEditViewProvider
     */
    private $adminRoleEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Permissions\ViewProviders\AdminRoleEditViewProvider $adminRoleEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminRoleEditViewProvider = $adminRoleEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        $defaults = [
            'id' => 0,
            'name' => '',
            'parent_id' => 0,
            'left_id' => 0,
            'right_id' => 0,
        ];

        return ($this->adminRoleEditViewProvider)($defaults);
    }
}
