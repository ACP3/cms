<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Categories\Test\View\Block\Admin;

use ACP3\Core\Modules\Modules;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Categories\View\Block\Admin\CategoryManageFormBlock;

class CategoryManageFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var Modules|\PHPUnit_Framework_MockObject_MockObject
     */
    private $modules;
    /**
     * @var CategoriesRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $categoriesRepository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->modules = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->modules->expects($this->once())
            ->method('getActiveModules')
            ->willReturn([
                'foo' => [
                    'id' => 1,
                    'active' => true,
                    'dependencies' => ['categories'],
                ],
            ]);

        $this->categoriesRepository = $this->getMockBuilder(CategoriesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->categoriesRepository->expects($this->never())
            ->method('getAllByModuleId');
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new CategoryManageFormBlock($this->context, $this->categoriesRepository, $this->modules);
    }

    /**
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'category_tree',
            'mod_list',
            'form_token',
        ];
    }
}
