<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share;

class Index extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Share\ViewProviders\DataGridViewProvider
     */
    private $dataGridViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Share\ViewProviders\DataGridViewProvider $dataGridViewProvider
    ) {
        parent::__construct($context);

        $this->dataGridViewProvider = $dataGridViewProvider;
    }

    /**
     * @return array|array[]|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function execute()
    {
        return ($this->dataGridViewProvider)();
    }
}
