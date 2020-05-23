<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\Helpers\FormToken;

class NewsletterSubscribeWidgetViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;

    public function __construct(FormToken $formTokenHelper)
    {
        $this->formTokenHelper = $formTokenHelper;
    }

    public function __invoke(): array
    {
        return [
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
