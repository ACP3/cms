<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Test\View\Block\Admin;

use ACP3\Core\Model\Repository\ModuleAwareRepositoryInterface;
use ACP3\Core\Test\View\Block\AbstractDataGridBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Comments\View\Block\Admin\CommentsDataGridBlock;

class CommentsDataGridBlockTest extends AbstractDataGridBlockTest
{

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $systemRepository = $this->getMockBuilder(ModuleAwareRepositoryInterface::class)
            ->setMethods([
                'getModuleId',
                'getModuleSchemaVersion',
                'moduleExists',
                'getInfoByModuleName',
                'getModuleNameById',
                'insert',
                'update',
                'delete',
                'getOneById',
                'getTableName'
            ])
            ->getMock();

        return new CommentsDataGridBlock($this->context, $systemRepository);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['moduleId' => 1]);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['moduleId' => 1]);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'grid',
            'module_id',
            'show_mass_delete_button'
        ];
    }
}
