<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Test\View\Block\Admin;

use ACP3\Core\ACL\ACLInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractDataGridBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Files\View\Block\Admin\FilesDataGridBlock;

class FilesDataGridBlockTest extends AbstractDataGridBlockTest
{
    /**
     * @var ACLInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $acl;
    /**
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settings;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->acl = $this->getMockBuilder(ACLInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $this->settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'order_by' => 'date',
            ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new FilesDataGridBlock($this->context, $this->acl, $this->settings);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'grid',
            'show_mass_delete_button',
        ];
    }
}
