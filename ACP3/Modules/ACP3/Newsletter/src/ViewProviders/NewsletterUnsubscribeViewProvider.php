<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;

class NewsletterUnsubscribeViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;

    public function __construct(FormToken $formTokenHelper, RequestInterface $request)
    {
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
    }

    public function __invoke(): array
    {
        $defaults = [
            'mail' => '',
        ];

        return [
            'form' => array_merge($defaults, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
