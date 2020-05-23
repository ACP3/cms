<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Permissions\Controller\Admin\Resources;

use ACP3\Core;
use ACP3\Modules\ACP3\Permissions;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Permissions\ViewProviders\ResourceListDataGridViewProvider
     */
    private $resourceListDataGridViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Permissions\ViewProviders\ResourceListDataGridViewProvider $resourceListDataGridViewProvider
    ) {
        parent::__construct($context);

        $this->resourceListDataGridViewProvider = $resourceListDataGridViewProvider;
    }

    /**
     * @return array
     */
    public function execute(): array
    {
        return ($this->resourceListDataGridViewProvider)();
    }
}
