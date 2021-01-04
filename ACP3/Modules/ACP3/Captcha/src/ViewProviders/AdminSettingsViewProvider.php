<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Helpers\FormToken;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AdminSettingsViewProvider
{
    /**
     * @var \ACP3\Core\Helpers\FormToken
     */
    private $formToken;
    /**
     * @var \ACP3\Core\Helpers\Forms
     */
    private $forms;
    /**
     * @var \Symfony\Component\DependencyInjection\ServiceLocator
     */
    private $captchaLocator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        FormToken $formToken,
        Forms $forms,
        ServiceLocator $captchaLocator,
        RequestInterface $request,
        SettingsInterface $settings
    ) {
        $this->formToken = $formToken;
        $this->forms = $forms;
        $this->captchaLocator = $captchaLocator;
        $this->request = $request;
        $this->settings = $settings;
    }

    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $captchas = [];
        foreach ($this->captchaLocator->getProvidedServices() as $serviceId => $class) {
            /** @var \ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface $captcha */
            $captcha = $this->captchaLocator->get($serviceId);

            $captchas[$serviceId] = $captcha->getCaptchaName();
        }

        return [
            'captchas' => $this->forms->choicesGenerator('captcha', $captchas, $settings['captcha']),
            'form' => \array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }
}
