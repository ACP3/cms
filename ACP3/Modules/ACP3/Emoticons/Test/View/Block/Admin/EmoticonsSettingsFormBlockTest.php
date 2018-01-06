<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Test\View\Block\Admin;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Emoticons\View\Block\Admin\EmoticonsSettingsFormBlock;

class EmoticonsSettingsFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('emoticons')
            ->willReturn([]);
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new EmoticonsSettingsFormBlock($this->context, $this->settings);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'form_token',
        ];
    }
}
