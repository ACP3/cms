<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Menus;

class Create extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\ViewProviders\AdminMenuEditViewProvider
     */
    private $adminMenuEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Menus\ViewProviders\AdminMenuEditViewProvider $adminMenuEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminMenuEditViewProvider = $adminMenuEditViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->adminMenuEditViewProvider)(['index_name' => '', 'title' => '']);
    }
}
