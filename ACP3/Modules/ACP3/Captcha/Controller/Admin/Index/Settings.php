<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\View\Block\SettingsFormBlockInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Captcha\Validation\AdminSettingsFormValidation;

class Settings extends AbstractFrontendAction
{
    /**
     * @var AdminSettingsFormValidation
     */
    private $formValidation;
    /**
     * @var SettingsFormBlockInterface
     */
    private $block;

    /**
     * Settings constructor.
     * @param FrontendContext $context
     * @param SettingsFormBlockInterface $block
     * @param AdminSettingsFormValidation $formValidation
     */
    public function __construct(
        FrontendContext $context,
        SettingsFormBlockInterface $block,
        AdminSettingsFormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->formValidation = $formValidation;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block->setRequestData($this->request->getPost()->all())->render();
    }

    /**
     * @return array|string|\Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
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
