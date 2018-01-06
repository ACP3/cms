<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Test\View\Block\Admin;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Files\View\Block\Admin\FilesSettingsFormBlock;

class FilesSettingsFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var Date|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateHelper;
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->dateHelper = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'order_by' => 'date',
                'dateformat' => 'long',
                'sidebar' => 5,
            ]);
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new FilesSettingsFormBlock($this->context, $this->dateHelper, $this->settings);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'order_by',
            'dateformat',
            'sidebar_entries',
            'form_token',
            'comments',
        ];
    }
}
