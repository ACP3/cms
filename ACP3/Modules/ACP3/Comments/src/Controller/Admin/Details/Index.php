<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Details;

use ACP3\Core;
use ACP3\Modules\ACP3\Comments;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\ViewProviders\DataGridByModuleViewProvider
     */
    private $dataGridByModuleViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Comments\ViewProviders\DataGridByModuleViewProvider $dataGridByModuleViewProvider
    ) {
        parent::__construct($context);

        $this->dataGridByModuleViewProvider = $dataGridByModuleViewProvider;
    }

    /**
     * @return array|array[]|\Symfony\Component\HttpFoundation\JsonResponse
     */
    public function execute(int $id)
    {
        return ($this->dataGridByModuleViewProvider)($id);
    }
}
