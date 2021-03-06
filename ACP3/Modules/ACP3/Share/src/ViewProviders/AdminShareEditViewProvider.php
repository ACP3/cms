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
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formTokenHelper;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\ShareFormFields
     */
    private $shareFormFields;

    public function __construct(
        FormToken $formTokenHelper,
        RequestInterface $request,
        ShareFormFields $shareFormFields
    ) {
        $this->formTokenHelper = $formTokenHelper;
        $this->request = $request;
        $this->shareFormFields = $shareFormFields;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(array $sharingInfo): array
    {
        return [
            'SHARE_FORM_FIELDS' => $this->shareFormFields->formFields($sharingInfo['uri']),
            'form' => \array_merge(['uri' => $sharingInfo['uri']], $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
    }
}
