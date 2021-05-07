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
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formToken;

    public function __construct(FormToken $formToken, RequestInterface $request)
    {
        $this->request = $request;
        $this->formToken = $formToken;
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
