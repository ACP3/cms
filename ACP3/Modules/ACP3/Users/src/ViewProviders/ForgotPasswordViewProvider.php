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
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formToken;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(FormToken $formToken, RequestInterface $request)
    {
        $this->formToken = $formToken;
        $this->request = $request;
    }

    public function __invoke(): array
    {
        return [
            'form' => \array_merge(['nick_mail' => ''], $this->request->getPost()->all()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }
}
