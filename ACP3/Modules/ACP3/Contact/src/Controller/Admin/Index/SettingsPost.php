<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Contact;

class SettingsPost extends Core\Controller\AbstractWidgetAction
{
    public function __construct(
        WidgetContext $context,
        private FormAction $actionHelper,
        private Core\Helpers\Secure $secureHelper,
        private Contact\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);
    }

    /**
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'address' => $formData['address'],
                'ceo' => $this->secureHelper->strEncode($formData['ceo']),
                'disclaimer' => $formData['disclaimer'],
                'fax' => $this->secureHelper->strEncode($formData['fax']),
                'mail' => $formData['mail'],
                'mobile_phone' => $this->secureHelper->strEncode($formData['mobile_phone']),
                'picture_credits' => $formData['picture_credits'],
                'telephone' => $this->secureHelper->strEncode($formData['telephone']),
                'vat_id' => $this->secureHelper->strEncode($formData['vat_id']),
            ];

            return $this->config->saveSettings($data, Contact\Installer\Schema::MODULE_NAME);
        });
    }
}
