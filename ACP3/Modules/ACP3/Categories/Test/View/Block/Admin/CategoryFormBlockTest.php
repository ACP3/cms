<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Categories\Test\View\Block\Admin;

use ACP3\Core\Modules;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Categories\View\Block\Admin\CategoryFormBlock;

class CategoryFormBlockTest extends AbstractFormBlockTest
{

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $modules = $this->getMockBuilder(Modules::class)
            ->disableOriginalConstructor()
            ->getMock();

        $modules->expects($this->once())
            ->method('getActiveModules')
            ->willReturn([
                'foo' => [
                    'id' => 1,
                    'active' => true,
                    'dependencies' => ['categories']
                ]
            ]);

        $categoriesRepository = $this->getMockBuilder(CategoriesRepository::class)
            ->disableOriginalConstructor()
            ->getMock();

        $categoriesRepository->expects($this->never())
            ->method('getAllByModuleId');

        return new CategoryFormBlock($this->context, $modules, $categoriesRepository);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'category_tree',
            'mod_list',
            'form_token'
        ];
    }
}
