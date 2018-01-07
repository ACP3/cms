<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Test\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Categories\Helpers;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use ACP3\Modules\ACP3\Files\View\Block\Admin\FileManageFormBlock;

class FileManageFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var FilesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $filesRepository;
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;
    /**
     * @var Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    private $modules;
    /**
     * @var Helpers|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoriesHelpers;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->filesRepository = $this->getMockBuilder(FilesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'comments' => 1,
            ]);

        $this->modules = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoriesHelpers = $this->getMockBuilder(Helpers::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new FileManageFormBlock(
            $this->context,
            $this->filesRepository,
            $this->settings,
            $this->modules,
            $this->categoriesHelpers
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'active',
            'options',
            'units',
            'categories',
            'external',
            'current_file',
            'form',
            'form_token',
            'SEO_URI_PATTERN',
            'SEO_ROUTE_NAME',
        ];
    }
}
