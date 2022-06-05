<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Seo;
use Symfony\Component\HttpFoundation\JsonResponse;

class Index extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        Core\Controller\Context\Context $context,
        private readonly Seo\ViewProviders\DataGridViewProvider $dataGridViewProvider
    ) {
        parent::__construct($context);
    }

    /**
     * @return array<string, mixed>|JsonResponse
     */
    public function __invoke(): array|JsonResponse
    {
        return ($this->dataGridViewProvider)();
    }
}
