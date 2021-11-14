<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Auditlog\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Auditlog\ViewProviders\DataGridByTableViewProvider;

class Table extends AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        private DataGridByTableViewProvider $dataGridByTableViewProvider
    ) {
        parent::__construct($context);
    }

    public function __invoke(string $table)
    {
        return ($this->dataGridByTableViewProvider)($table);
    }
}
