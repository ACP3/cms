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
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $acl = $this->getMockBuilder(ACLInterface::class)
            ->disableOriginalConstructor()
            ->getMockForAbstractClass();

        $settings = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $settings->expects($this->once())
            ->method('getSettings')
            ->with('files')
            ->willReturn([
                'order_by' => 'date',
            ]);

        return new FilesDataGridBlock($this->context, $acl, $settings);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'grid',
            'show_mass_delete_button',
        ];
    }
}
