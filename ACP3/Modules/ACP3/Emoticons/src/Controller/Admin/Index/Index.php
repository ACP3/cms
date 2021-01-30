<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Emoticons\ViewProviders\DataGridViewProvider;

class Index extends AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\ViewProviders\DataGridViewProvider
     */
    private $dataGridViewProvider;

    public function __construct(
        WidgetContext $context,
        DataGridViewProvider $dataGridViewProvider
    ) {
        parent::__construct($context);

        $this->dataGridViewProvider = $dataGridViewProvider;
    }

    public function execute()
    {
        return ($this->dataGridViewProvider)();
    }
}
