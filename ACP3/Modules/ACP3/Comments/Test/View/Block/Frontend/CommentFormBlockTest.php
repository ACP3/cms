<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Comments\Test\View\Block\Frontend;

use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Comments\View\Block\Frontend\CommentFormBlock;
use ACP3\Modules\ACP3\Users\Model\UserModel;

class CommentFormBlockTest extends AbstractFormBlockTest
{

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $settingsMock = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $settingsMock->expects($this->once())
            ->method('getSettings')
            ->willReturn(['emoticons' => 1]);

        $user = $this->getMockBuilder(UserModel::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new CommentFormBlock($this->context, $settingsMock, $user);
    }

    public function testRenderReturnsArray()
    {
        $this->block->setData(['module' => 'foo', 'entryId' => 2, 'redirectUrl' => 'foo/index/details/id_2']);

        parent::testRenderReturnsArray();
    }

    public function testRenderReturnsArrayWithExpectedKeys()
    {
        $this->block->setData(['module' => 'foo', 'entryId' => 2, 'redirectUrl' => 'foo/index/details/id_2']);

        parent::testRenderReturnsArrayWithExpectedKeys();
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'module',
            'entry_id',
            'redirect_url',
            'form_token',
            'can_use_emoticons'
        ];
    }
}
