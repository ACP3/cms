<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Unsubscribe extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\ViewProviders\NewsletterUnsubscribeViewProvider
     */
    private $newsletterUnsubscribeViewProvider;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Newsletter\ViewProviders\NewsletterUnsubscribeViewProvider $newsletterUnsubscribeViewProvider
    ) {
        parent::__construct($context);

        $this->newsletterUnsubscribeViewProvider = $newsletterUnsubscribeViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->newsletterUnsubscribeViewProvider)();
    }
}
