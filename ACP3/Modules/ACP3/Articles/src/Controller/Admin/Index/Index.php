<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Articles\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Articles\ViewProviders\DataGridViewProvider;

class Index extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        private DataGridViewProvider $dataGridViewProvider
    ) {
        parent::__construct($context);
    }

    public function __invoke()
    {
        return ($this->dataGridViewProvider)();
    }
}
