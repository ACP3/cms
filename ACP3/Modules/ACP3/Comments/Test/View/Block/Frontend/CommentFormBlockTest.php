<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
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
     * @var SettingsInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $settingsMock;
    /**
     * @var UserModel|\PHPUnit_Framework_MockObject_MockObject
     */
    private $user;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->settingsMock = $this->getMockBuilder(SettingsInterface::class)
            ->setMethods(['getSettings', 'saveSettings'])
            ->getMock();

        $this->settingsMock->expects($this->once())
            ->method('getSettings')
            ->willReturn(['emoticons' => 1]);

        $this->user = $this->getMockBuilder(UserModel::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * {@inheritdoc}
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new CommentFormBlock($this->context, $this->settingsMock, $this->user);
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
     * {@inheritdoc}
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'module',
            'entry_id',
            'redirect_url',
            'form_token',
            'can_use_emoticons',
        ];
    }
}
