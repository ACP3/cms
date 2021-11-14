<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Index extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private Permissions\ViewProviders\ResourceListDataGridViewProvider $resourceListDataGridViewProvider
    ) {
        parent::__construct($context);
    }

    public function __invoke(): array
    {
        return ($this->resourceListDataGridViewProvider)();
    }
}
