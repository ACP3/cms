<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Captcha\Validation\AdminSettingsFormValidation;
use ACP3\Modules\ACP3\Captcha\ViewProviders\AdminSettingsViewProvider;

class Settings extends AbstractFrontendAction
{
    /**
     * @var AdminSettingsFormValidation
     */
    private $formValidation;
    /**
     * @var \ACP3\Modules\ACP3\Captcha\ViewProviders\AdminSettingsViewProvider
     */
    private $adminSettingsViewProvider;

    public function __construct(
        FrontendContext $context,
        AdminSettingsViewProvider $adminSettingsViewProvider,
        AdminSettingsFormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->adminSettingsViewProvider = $adminSettingsViewProvider;
    }

    public function execute(): array
    {
        return ($this->adminSettingsViewProvider)();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @throws \Doctrine\DBAL\ConnectionException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->formValidation->validate($formData);

            return $this->config->saveSettings($formData, Schema::MODULE_NAME);
        });
    }
}
