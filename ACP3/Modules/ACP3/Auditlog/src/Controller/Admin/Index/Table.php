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
    /**
     * @var \ACP3\Modules\ACP3\Auditlog\ViewProviders\DataGridByTableViewProvider
     */
    private $dataGridByTableViewProvider;

    public function __construct(
        WidgetContext $context,
        DataGridByTableViewProvider $dataGridByTableViewProvider
    ) {
        parent::__construct($context);

        $this->dataGridByTableViewProvider = $dataGridByTableViewProvider;
    }

    public function __invoke(string $table)
    {
        return ($this->dataGridByTableViewProvider)($table);
    }
}
