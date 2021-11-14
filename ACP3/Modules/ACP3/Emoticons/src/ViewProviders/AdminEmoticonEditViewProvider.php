<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;

class AdminEmoticonEditViewProvider
{
    public function __construct(private FormToken $formTokenHelper, private RequestInterface $request)
    {
    }

    public function __invoke(array $emoticon): array
    {
        return [
            'form' => array_merge($emoticon, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
