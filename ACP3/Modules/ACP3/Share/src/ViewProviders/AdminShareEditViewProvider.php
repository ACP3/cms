<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\ViewProviders;

use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Share\Helpers\ShareFormFields;

class AdminShareEditViewProvider
{
    public function __construct(private FormToken $formTokenHelper, private RequestInterface $request, private ShareFormFields $shareFormFields)
    {
    }

    /**
     * @param array<string, mixed> $sharingInfo
     *
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $sharingInfo): array
    {
        return [
            'SHARE_FORM_FIELDS' => $this->shareFormFields->formFields($sharingInfo['uri']),
            'form' => array_merge(['uri' => $sharingInfo['uri']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
