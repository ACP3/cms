<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Create extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\Context                                 $context,
        private Permissions\ViewProviders\AdminResourceEditViewProvider $adminResourceEditViewProvider
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>
     * @throws \ReflectionException
     */
    public function __invoke(): array
    {
        $defaults = [
            'page' => '',
            'area' => '',
            'controller' => '',
            'module_name' => null,
        ];

        return ($this->adminResourceEditViewProvider)($defaults);
    }
}
