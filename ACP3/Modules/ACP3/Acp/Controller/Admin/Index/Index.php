<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Acp\Controller\Admin\Index;

use ACP3\Core;
use ACP3\Core\Controller\Context;

class Index extends Core\Controller\AbstractFrontendAction
{
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * Index constructor.
     * @param Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     */
    public function __construct(Context\FrontendContext $context, Core\View\Block\BlockInterface $block)
    {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * @return array
     */
    public function execute()
    {
        return $this->block->render();
    }
}
