<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Captcha\View\Block\Admin;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View\Block\AbstractSettingsFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;
use ACP3\Modules\ACP3\Captcha\Installer\Schema;
use ACP3\Modules\ACP3\Captcha\Utility\CaptchaRegistrar;

class CaptchaSettingsFormBlock extends AbstractSettingsFormBlock
{
    /**
     * @var CaptchaRegistrar
     */
    private $captchaRegistrar;

    /**
     * SettingsFormBlock constructor.
     * @param FormBlockContext $context
     * @param SettingsInterface $settings
     * @param CaptchaRegistrar $captchaRegistrar
     */
    public function __construct(
        FormBlockContext $context,
        SettingsInterface $settings,
        CaptchaRegistrar $captchaRegistrar
    ) {
        parent::__construct($context, $settings);

        $this->captchaRegistrar = $captchaRegistrar;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $data = $this->getData();

        return [
            'captchas' => $this->forms->choicesGenerator(
                'captcha',
                $this->getAvailableCaptchas(),
                $data['captcha']
            ),
            'form' => \array_merge($data, $this->getRequestData()),
            'form_token' => $this->formToken->renderFormToken(),
        ];
    }

    /**
     * @return array
     */
    private function getAvailableCaptchas(): array
    {
        $captchas = [];
        foreach ($this->captchaRegistrar->getAvailableCaptchas() as $serviceId => $captcha) {
            /** @var CaptchaExtensionInterface $captcha */
            $captchas[$serviceId] = $captcha->getCaptchaName();
        }

        return $captchas;
    }

    /**
     * @inheritdoc
     */
    public function getModuleName(): string
    {
        return Schema::MODULE_NAME;
    }
}
