<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Share\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Share;

class Settings extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    protected $formTokenHelper;
    /**
     * @var \ACP3\Modules\ACP3\Share\Validation\AdminSettingsFormValidation
     */
    protected $adminSettingsFormValidation;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    protected $formsHelper;
    /**
     * @var \ACP3\Core\Helpers\Secure
     */
    private $secure;
    /**
     * @var \ACP3\Modules\ACP3\Share\Helpers\SocialServices
     */
    private $socialServices;

    /**
     * Settings constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext                   $context
     * @param \ACP3\Core\Helpers\Forms                                        $formsHelper
     * @param \ACP3\Core\Helpers\FormToken                                    $formTokenHelper
     * @param \ACP3\Core\Helpers\Secure                                       $secure
     * @param \ACP3\Modules\ACP3\Share\Helpers\SocialServices                 $socialServices
     * @param \ACP3\Modules\ACP3\Share\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Helpers\Forms $formsHelper,
        Core\Helpers\FormToken $formTokenHelper,
        Core\Helpers\Secure $secure,
        Share\Helpers\SocialServices $socialServices,
        Share\Validation\AdminSettingsFormValidation $adminSettingsFormValidation
    ) {
        parent::__construct($context);

        $this->formsHelper = $formsHelper;
        $this->formTokenHelper = $formTokenHelper;
        $this->adminSettingsFormValidation = $adminSettingsFormValidation;
        $this->secure = $secure;
        $this->socialServices = $socialServices;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $shareSettings = $this->config->getSettings(Share\Installer\Schema::MODULE_NAME);

        return [
            'services' => $this->formsHelper->choicesGenerator(
                'services',
                $this->getServices(),
                \unserialize($shareSettings['services'])
            ),
            'form' => \array_merge($shareSettings, $this->request->getPost()->all()),
            'form_token' => $this->formTokenHelper->renderFormToken(),
        ];
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
                'fb_app_id' => $this->secure->strEncode($formData['fb_app_id']),
                'fb_secret' => $this->secure->strEncode($formData['fb_secret']),
                'services' => \serialize($formData['services']),
            ];

            return $this->config->saveSettings($data, Share\Installer\Schema::MODULE_NAME);
        });
    }

    private function getServices(): array
    {
        $services = [];
        foreach ($this->socialServices->getAllServices() as $service) {
            $services[$service] = $this->translator->t('share', 'service_' . $service);
        }

        return $services;
    }
}
