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
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $settings->expects($this->once())
            ->method('getSettings')
            ->with('emoticons')
            ->willReturn([]);

        return new EmoticonsSettingsFormBlock($this->context, $settings);
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
