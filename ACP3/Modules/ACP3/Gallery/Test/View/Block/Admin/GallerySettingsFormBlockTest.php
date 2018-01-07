<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Test\View\Block\Admin;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Gallery\View\Block\Admin\GallerySettingsFormBlock;

class GallerySettingsFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;
    /**
     * @var Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    private $modules;
    /**
     * @var Date|\PHPUnit_Framework_MockObject_MockObject
     */
    private $dateHelper;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('gallery')
            ->willReturn([
                'comments' => 1,
                'sidebar' => 5,
                'dateformat' => 'long',
                'overlay' => 1,
            ]);

        $this->modules = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->dateHelper = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new GallerySettingsFormBlock($this->context, $this->settings, $this->modules, $this->dateHelper);
    }

    public function testRenderReturnsArray()
    {
        parent::testRenderReturnsArray();
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'overlay',
            'dateformat',
            'sidebar_entries',
            'form',
            'form_token',
            'comments',
        ];
    }
}
