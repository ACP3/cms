<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

/**
 * Created by PhpStorm.
 * User: tinog
 * Date: 23.04.2017
 * Time: 12:17
 */

namespace ACP3\Modules\ACP3\Captcha\Test\View\Block\Admin;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Captcha\Extension\CaptchaExtensionInterface;
use ACP3\Modules\ACP3\Captcha\Utility\CaptchaRegistrar;
use ACP3\Modules\ACP3\Captcha\View\Block\Admin\CaptchaSettingsFormBlock;

class CaptchaSettingsFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $settings->expects($this->once())
            ->method('getSettings')
            ->with('captcha')
            ->willReturn(['captcha' => 'captcha.extension.foo']);

        $captchaRegistrar = $this->getMockBuilder(CaptchaRegistrar::class)
            ->getMock();

        $captchaMock = $this->getMockBuilder(CaptchaExtensionInterface::class)
            ->setMethods(['getCaptchaName', 'isCaptchaValid', 'getCaptcha'])
            ->getMock();

        $captchaRegistrar->expects($this->once())
            ->method('getAvailableCaptchas')
            ->willReturn([
                'captcha.extension.foo' => $captchaMock,
            ]);

        return new CaptchaSettingsFormBlock($this->context, $settings, $captchaRegistrar);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'captchas',
            'form',
            'form_token',
        ];
    }
}
