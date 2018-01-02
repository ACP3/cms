<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Newsletter;

class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Modules\ACP3\Newsletter\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    protected $secureHelper;
    /**
     * @var Core\View\Block\SettingsFormBlockInterface
     */
    private $block;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\SettingsFormBlockInterface $block
     * @param \ACP3\Core\Helpers\Secure $secureHelper
     * @param \ACP3\Modules\ACP3\Newsletter\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\SettingsFormBlockInterface $block,
        Core\Helpers\Secure $secureHelper,
        Newsletter\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->secureHelper = $secureHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block
            ->setRequestData($this->request->getPost()->all())
            ->render();
    }

    /**
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function executePost()
    {
        return $this->actionHelper->handleSettingsPostAction(function () {
            $formData = $this->request->getPost()->all();

            $this->adminSettingsFormValidation->validate($formData);

            $data = [
                'mail' => $formData['mail'],
                'mailsig' => $this->secureHelper->strEncode($formData['mailsig'], true),
                'html' => (int)$formData['html']
            ];

            return $this->config->saveSettings($data, Newsletter\Installer\Schema::MODULE_NAME);
        });
    }
}
