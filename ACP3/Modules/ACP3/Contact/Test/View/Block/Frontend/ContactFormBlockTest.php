<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Contact\Test\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Contact\View\Frontend\ContactFormBlock;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class ContactFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;
    /**
     * @var UserModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $user;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('contact')
            ->willReturn([]);

        $this->user = $this->getMockBuilder(UserModel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new ContactFormBlock($this->context, $this->settings, $this->user);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'copy',
            'contact',
            'form_token',
        ];
    }
}
