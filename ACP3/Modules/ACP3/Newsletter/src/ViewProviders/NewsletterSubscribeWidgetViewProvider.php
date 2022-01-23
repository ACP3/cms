<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\Helpers\FormToken;

class NewsletterSubscribeWidgetViewProvider
{
    public function __construct(private FormToken $formTokenHelper)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
