<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

class Index extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        private Comments\ViewProviders\DataGridByModuleViewProvider $dataGridByModuleViewProvider
    ) {
        parent::__construct($context);
    }

    /**
     * @return array|array[]|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function __invoke(int $id): array|\Symfony\Component\HttpFoundation\JsonResponse
    {
        return ($this->dataGridByModuleViewProvider)($id);
    }
}
