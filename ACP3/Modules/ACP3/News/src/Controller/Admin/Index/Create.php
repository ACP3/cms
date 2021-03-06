<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\News;

class Create extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\News\ViewProviders\AdminNewsEditViewProvider
     */
    private $adminNewsEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        News\ViewProviders\AdminNewsEditViewProvider $adminNewsEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminNewsEditViewProvider = $adminNewsEditViewProvider;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        $defaults = [
            'active' => 1,
            'category_id' => null,
            'readmore' => 0,
            'id' => null,
            'title' => '',
            'target' => null,
            'text' => '',
            'uri' => '',
            'link_title' => '',
            'start' => '',
            'end' => '',
        ];

        return ($this->adminNewsEditViewProvider)($defaults);
    }
}
