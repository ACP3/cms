<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Files;

class Create extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Files\ViewProviders\AdminFileEditViewProvider
     */
    private $adminFileEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Files\ViewProviders\AdminFileEditViewProvider $adminFileEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminFileEditViewProvider = $adminFileEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $defaults = [
            'id' => null,
            'category_id' => null,
            'active' => 1,
            'title' => '',
            'file' => '',
            'file_internal' => '',
            'file_external' => '',
            'filesize' => '',
            'size' => null,
            'text' => '',
            'start' => '',
            'end' => '',
        ];

        return ($this->adminFileEditViewProvider)($defaults);
    }
}
