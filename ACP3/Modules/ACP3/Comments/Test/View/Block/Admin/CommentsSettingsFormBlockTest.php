<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Test\View\Block\Admin;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Comments\View\Block\Admin\CommentsSettingsFormBlock;

class CommentsSettingsFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    private $modules;
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;
    /**
     * @var Date|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateHelper;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->modules = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('comments')
            ->willReturn(['emoticons' => 1, 'dateformat' => 'long']);

        $this->dateHelper = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new CommentsSettingsFormBlock($this->context, $this->modules, $this->settings, $this->dateHelper);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'dateformat',
            'form_token',
            'allow_emoticons',
        ];
    }
}
