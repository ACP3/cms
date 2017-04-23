<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Comments\View\Block\Admin\CommentFormBlock;

class CommentFormBlockTest extends AbstractFormBlockTest
{

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new CommentFormBlock($this->context);
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
            'can_use_emoticons'
        ];
    }
}
