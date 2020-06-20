<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Emoticons;
use ACP3\Modules\ACP3\Emoticons\Validation\AdminSettingsFormValidation;
use ACP3\Modules\ACP3\Emoticons\ViewProviders\AdminSettingsViewProvider;

class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\Validation\AdminSettingsFormValidation
     */
    private $adminSettingsFormValidation;
    /**
     * @var \ACP3\Modules\ACP3\Emoticons\ViewProviders\AdminSettingsViewProvider
     */
    private $adminSettingsViewProvider;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        FrontendContext $context,
        Action $actionHelper,
        AdminSettingsFormValidation $adminSettingsFormValidation,
        AdminSettingsViewProvider $adminSettingsViewProvider
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->adminSettingsViewProvider = $adminSettingsViewProvider;
        $this->actionHelper = $actionHelper;
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
            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'width' => (int) $formData['width'],
                'height' => (int) $formData['height'],
                'filesize' => (int) $formData['filesize'],
            ];

            return $this->config->saveSettings($data, Emoticons\Installer\Schema::MODULE_NAME);
        });
    }
}
