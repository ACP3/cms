<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Index extends Core\Controller\AbstractWidgetAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterSubscribeViewProvider
     */
    private $newsletterSubscribeViewProvider;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Newsletter\ViewProviders\NewsletterSubscribeViewProvider $newsletterSubscribeViewProvider
    ) {
        parent::__construct($context);

        $this->newsletterSubscribeViewProvider = $newsletterSubscribeViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->newsletterSubscribeViewProvider)();
    }
}
