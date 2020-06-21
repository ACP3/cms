<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Newsletter;

class SettingsPost extends Core\Controller\AbstractFrontendAction implements Core\Controller\InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\AdminSettingsFormValidation
     */
    private $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secureHelper;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Action $actionHelper,
        Core\Helpers\Secure $secureHelper,
        Newsletter\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'mail' => $formData['mail'],
                'mailsig' => $this->secureHelper->strEncode($formData['mailsig'], true),
                'html' => (int) $formData['html'],
            ];

            return $this->config->saveSettings($data, Newsletter\Installer\Schema::MODULE_NAME);
        });
    }
}
