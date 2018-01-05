<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Emoticons\Test\View\Block\Admin;

use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Test\View\Block\AbstractDataGridBlockTest;
use ACP3\Core\View\Block\BlockInterface;
use ACP3\Modules\ACP3\Emoticons\View\Block\Admin\EmoticonsDataGridBlock;

class EmoticonsDataGridBlockTest extends AbstractDataGridBlockTest
{
    /**
     * @inheritdoc
     */
    protected function instantiateBlock(): BlockInterface
    {
        $appPath = $this->getMockBuilder(ApplicationPath::class)
            ->disableOriginalConstructor()
            ->getMock();

        return new EmoticonsDataGridBlock($this->context, $appPath);
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
