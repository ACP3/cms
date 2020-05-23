<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterSubscribeWidgetViewProvider;

class Index extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterSubscribeWidgetViewProvider
     */
    private $newsletterSubscribeWidgetViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        NewsletterSubscribeWidgetViewProvider $newsletterSubscribeWidgetViewProvider
    ) {
        parent::__construct($context);

        $this->newsletterSubscribeWidgetViewProvider = $newsletterSubscribeWidgetViewProvider;
    }

    public function execute(string $template = ''): array
    {
        $this->setTemplate($template);

        return ($this->newsletterSubscribeWidgetViewProvider)();
    }
}
