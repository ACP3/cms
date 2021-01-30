<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share\ViewProviders\AdminShareEditViewProvider;

class Create extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Share\ViewProviders\AdminShareEditViewProvider
     */
    private $adminShareEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        AdminShareEditViewProvider $adminShareEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminShareEditViewProvider = $adminShareEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(): array
    {
        $defaults = [
            'uri' => '',
        ];

        return ($this->adminShareEditViewProvider)($defaults);
    }
}
