<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\Test\View\Block\Admin;

use ACP3\Core\ACL\ACL;
use ACP3\Core\Modules;
use ACP3\Core\Test\View\Block\AbstractBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Acp\View\Block\Admin\AllowedModulesBlock;

class AllowedModulesBlockTest extends AbstractBlockTest
{

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $acl = $this->getMockBuilder(ACL::class)
            ->disableOriginalConstructor()
            ->setMethods(['hasPermission'])
            ->getMock();

        $modules = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->setMethods(['getActiveModules'])
            ->getMock();

        $modules->expects($this->once())
            ->method('getActiveModules')
            ->willReturn([
                'foo' => [
                    'dir' => 'foo/'
                ]
            ]);

        return new AllowedModulesBlock($this->context, $acl, $modules);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'modules'
        ];
    }
}
