<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Users\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;

class ForgotPasswordViewProvider
{
    public function __construct(private readonly FormToken $formToken, private readonly RequestInterface $request)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'form' => array_merge(['nick_mail' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }
}
