<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Files\ViewProviders\DataGridViewProvider
     */
    private $dataGridViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Files\ViewProviders\DataGridViewProvider $dataGridViewProvider
    ) {
        parent::__construct($context);

        $this->dataGridViewProvider = $dataGridViewProvider;
    }

    public function execute()
    {
        return ($this->dataGridViewProvider)();
    }
}
