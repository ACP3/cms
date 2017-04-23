<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Gallery\Test\View\Block\Admin;

use ACP3\Core\Helpers\Date;
use ACP3\Core\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Gallery\View\Block\Admin\GallerySettingsFormBlock;

class GallerySettingsFormBlockTest extends AbstractFormBlockTest
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
            ->with('gallery')
            ->willReturn([
                'comments' => 1,
                'sidebar' => 5,
                'dateformat' => 'long',
                'overlay' => 1
            ]);

        $modules = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->getMock();

        $dateHelper = $this->getMockBuilder(Date::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new GallerySettingsFormBlock($this->context, $settings, $modules, $dateHelper);
    }

    public function testRenderReturnsArray()
    {
        parent::testRenderReturnsArray();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'overlay',
            'dateformat',
            'sidebar_entries',
            'form',
            'form_token'
        ];
    }
}
