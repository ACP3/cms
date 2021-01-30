<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractWidgetAction;
use ACP3\Core\Controller\Context\WidgetContext;
use ACP3\Core\Controller\InvokableActionInterface;
use ACP3\Core\Modules\Helper\Action;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Captcha\Validation\AdminSettingsFormValidation;

class SettingsPost extends AbstractWidgetAction implements InvokableActionInterface
{
    /**
     * @var AdminSettingsFormValidation
     */
    private $formValidation;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    private $actionHelper;

    public function __construct(
        WidgetContext $context,
        Action $actionHelper,
        AdminSettingsFormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
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

            $this->formValidation->validate($formData);

            return $this->config->saveSettings($formData, Schema::MODULE_NAME);
        });
    }
}
