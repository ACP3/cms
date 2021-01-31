<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Helpers\Secure;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Comments;
use ACP3\Modules\ACP3\Comments\Validation\AdminSettingsFormValidation;

class SettingsPost extends AbstractWidgetAction implements InvokableActionInterface
{
    /**
     * @var \ACP3\Modules\ACP3\Comments\Validation\AdminSettingsFormValidation
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
        WidgetContext $context,
        Action $actionHelper,
        Secure $secureHelper,
        AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secureHelper = $secureHelper;
        $this->actionHelper = $actionHelper;
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
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
                'dateformat' => $this->secureHelper->strEncode($formData['dateformat']),
            ];

            return $this->config->saveSettings($data, Comments\Installer\Schema::MODULE_NAME);
        });
    }
}
