<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;
    /**
     * @var CaptchaRegistrar|\PHPUnit_Framework_MockObject_MockObject
     */
    private $captchaRegistrar;
    /**
     * @var CaptchaExtensionInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $captchaMock;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('captcha')
            ->willReturn(['captcha' => 'captcha.extension.foo']);

        $this->captchaRegistrar = $this->getMockBuilder(CaptchaRegistrar::class)
            ->getMock();

        $this->captchaMock = $this->getMockBuilder(CaptchaExtensionInterface::class)
            ->setMethods(['getCaptchaName', 'isCaptchaValid', 'getCaptcha'])
            ->getMock();

        $this->captchaRegistrar->expects($this->once())
            ->method('getAvailableCaptchas')
            ->willReturn([
                'captcha.extension.foo' => $this->captchaMock,
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new CaptchaSettingsFormBlock($this->context, $this->settings, $this->captchaRegistrar);
    }

    /**
     * {@inheritdoc}
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
