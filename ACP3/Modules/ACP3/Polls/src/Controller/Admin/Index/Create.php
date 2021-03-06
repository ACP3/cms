<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Polls\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Polls;

class Create extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Polls\ViewProviders\AdminPollEditViewProvider
     */
    private $adminPollEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Polls\ViewProviders\AdminPollEditViewProvider $adminPollEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminPollEditViewProvider = $adminPollEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        $defaults = [
            'id' => null,
            'multiple' => 0,
            'title' => '',
            'start' => '',
            'end' => '',
        ];

        return ($this->adminPollEditViewProvider)($defaults);
    }
}
