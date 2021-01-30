<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Create extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\ViewProviders\AdminResourceEditViewProvider
     */
    private $adminResourceEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Permissions\ViewProviders\AdminResourceEditViewProvider $adminResourceEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminResourceEditViewProvider = $adminResourceEditViewProvider;
    }

    /**
     * @throws \ReflectionException
     */
    public function __invoke(): array
    {
        $defaults = [
            'page' => '',
            'area' => '',
            'controller' => '',
            'module_name' => null,
            'privilege_id' => 0,
        ];

        return ($this->adminResourceEditViewProvider)($defaults);
    }
}
