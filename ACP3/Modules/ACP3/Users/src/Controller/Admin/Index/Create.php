<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

class Create extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Users\ViewProviders\AdminUserEditViewProvider
     */
    private $adminUserEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Users\ViewProviders\AdminUserEditViewProvider $adminUserEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminUserEditViewProvider = $adminUserEditViewProvider;
    }

    public function __invoke(): array
    {
        $defaults = [
            'nickname' => '',
            'realname' => '',
            'street' => '',
            'house_number' => '',
            'zip' => '',
            'city' => '',
        ];

        return ($this->adminUserEditViewProvider)($defaults);
    }
}
