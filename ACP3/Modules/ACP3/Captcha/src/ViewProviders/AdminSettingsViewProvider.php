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
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use Symfony\Component\DependencyInjection\ServiceLocator;

class AdminSettingsViewProvider
{
    /**
     * @param ServiceLocator<CaptchaExtensionInterface> $captchaLocator
     */
    public function __construct(private readonly FormToken $formToken, private readonly Forms $forms, private readonly ServiceLocator $captchaLocator, private readonly RequestInterface $request, private readonly SettingsInterface $settings)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function __invoke(): array
    {
        $settings = $this->settings->getSettings(Schema::MODULE_NAME);

        $captchas = [];
        foreach ($this->captchaLocator->getProvidedServices() as $serviceId => $class) {
            /** @var CaptchaExtensionInterface $captcha */
            $captcha = $this->captchaLocator->get($serviceId);

            $captchas[$serviceId] = $captcha->getCaptchaName();
        }

        return [
            'captchas' => $this->forms->choicesGenerator('captcha', $captchas, $settings['captcha']),
            'form' => array_merge($settings, $this->request->getPost()->all()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }
}
