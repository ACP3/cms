<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;

class RegistrationViewProvider
{
    public function __construct(private FormToken $formToken, private RequestInterface $request)
    {
    }

    public function __invoke(): array
    {
        $defaults = [
            'nickname' => '',
            'mail' => '',
        ];

        return [
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }
}
