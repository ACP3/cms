<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\Controller\Admin\Index;

use ACP3\Core\Controller\AbstractFrontendAction;
use ACP3\Core\Controller\Context\FrontendContext;
use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Captcha\Utility\CaptchaRegistrar;
use ACP3\Modules\ACP3\Captcha\Validation\AdminSettingsFormValidation;

class Settings extends AbstractFrontendAction
{
    /**
     * @var Forms
     */
    private $forms;
    /**
     * @var FormToken
     */
    private $formToken;
    /**
     * @var CaptchaRegistrar
     */
    private $captchaRegistrar;
    /**
     * @var AdminSettingsFormValidation
     */
    private $formValidation;

    /**
     * Settings constructor.
     *
     * @param FrontendContext             $context
     * @param Forms                       $forms
     * @param FormToken                   $formToken
     * @param CaptchaRegistrar            $captchaRegistrar
     * @param AdminSettingsFormValidation $formValidation
     */
    public function __construct(
        FrontendContext $context,
        Forms $forms,
        FormToken $formToken,
        CaptchaRegistrar $captchaRegistrar,
        AdminSettingsFormValidation $formValidation
    ) {
        parent::__construct($context);

        $this->forms = $forms;
        $this->formToken = $formToken;
        $this->captchaRegistrar = $captchaRegistrar;
        $this->formValidation = $formValidation;
    }

    /**
     * @return array
     */
    public function execute()
    {
        $settings = $this->config->getSettings(Schema::MODULE_NAME);

        $captchas = [];
        foreach ($this->captchaRegistrar->getAvailableCaptchas() as $serviceId => $captcha) {
            /* @var CaptchaExtensionInterface $captcha */
            $captchas[$serviceId] = $captcha->getCaptchaName();
        }

        return [
            'captchas' => $this->forms->choicesGenerator('captcha', $captchas, $settings['captcha']),
            'form' => \array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
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
