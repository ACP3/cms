<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Modules\ACP3\Contact;

class Index extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Contact\ViewProviders\ContactFormViewProvider
     */
    private $contactFormViewProvider;

    public function __construct(
        WidgetContext $context,
        Contact\ViewProviders\ContactFormViewProvider $contactFormViewProvider
    ) {
        parent::__construct($context);

        $this->contactFormViewProvider = $contactFormViewProvider;
    }

    public function __invoke(): array
    {
        return ($this->contactFormViewProvider)();
    }
}
