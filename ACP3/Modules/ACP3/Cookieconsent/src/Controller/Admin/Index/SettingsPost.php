<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Cookieconsent\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Helpers\FormAction;
use ACP3\Modules\ACP3\Cookieconsent\Installer\Schema;
use ACP3\Modules\ACP3\Cookieconsent\Validation\AdminSettingsFormValidation;

class SettingsPost extends AbstractWidgetAction
{
    /**
     * @var \ACP3\Modules\ACP3\Cookieconsent\Validation\AdminSettingsFormValidation
     */
    private $cookieConsentValidator;
    /**
     * @var \ACP3\Core\Helpers\FormAction
     */
    private $actionHelper;

    public function __construct(
        WidgetContext $context,
        FormAction $actionHelper,
        AdminSettingsFormValidation $cookieConsentValidator
    ) {
        parent::__construct($context);

        $this->cookieConsentValidator = $cookieConsentValidator;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke()
    {
        return $this->actionHelper->handleSettingsPostAction(
            function () {
                $formData = $this->request->getPost()->all();

                $this->cookieConsentValidator->validate($formData);

                $data = [
                    'enabled' => (int) $formData['enabled'],
                ];

                return $this->config->saveSettings($data, Schema::MODULE_NAME);
            }
        );
    }
}
