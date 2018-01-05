<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Comments\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Comments\Model\Repository\CommentsRepository;
use ACP3\Modules\ACP3\Comments\View\Block\Admin\CommentAdminFormBlock;

class CommentAdminFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var CommentsRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->repository = $this->getMockBuilder(CommentsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new CommentAdminFormBlock($this->context, $this->repository);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['module' => 'foo', 'module_id' => 1, 'name' => 'Foo']);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['module' => 'foo', 'module_id' => 1, 'name' => 'Foo']);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'module_id',
            'form_token',
            'can_use_emoticons',
        ];
    }
}
