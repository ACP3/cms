<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Create extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\AdminNewsletterEditViewProvider
     */
    private $adminNewsletterEditViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Newsletter\ViewProviders\AdminNewsletterEditViewProvider $adminNewsletterEditViewProvider
    ) {
        parent::__construct($context);

        $this->adminNewsletterEditViewProvider = $adminNewsletterEditViewProvider;
    }

    public function __invoke(): array
    {
        $defaults = [
            'action' => 1,
            'title' => '',
            'test' => 0,
            'text' => '',
            'date' => '',
        ];

        return ($this->adminNewsletterEditViewProvider)($defaults);
    }
}
