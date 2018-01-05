<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Test\View\Block\Admin;

use ACP3\Core\Test\View\Block\AbstractFormBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Emoticons\Model\Repository\EmoticonsRepository;
use ACP3\Modules\ACP3\Emoticons\View\Block\Admin\EmoticonManageFormBlock;

class EmoticonManageFormBlockTest extends AbstractFormBlockTest
{
    /**
     * @var EmoticonsRepository|\PHPUnit_Framework_MockObject_MockObject
     */
    private $repository;

    protected function setUpMockObjects()
    {
        parent::setUpMockObjects();

        $this->repository = $this->getMockBuilder(EmoticonsRepository::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        return new EmoticonManageFormBlock($this->context, $this->repository);
    }

    /**
     * @inheritdoc
     */
    protected function getExpectedArrayKeys(): array
    {
        return [
            'form',
            'form_token',
        ];
    }
}
