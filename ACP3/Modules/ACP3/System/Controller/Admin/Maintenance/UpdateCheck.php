<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\System\Controller\Admin\Maintenance;

use ACP3\Core;
use ACP3\Core\Controller\Context;
use ACP3\Modules\ACP3\System;

class UpdateCheck extends Core\Controller\AbstractAdminAction
{
    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * UpdateCheck constructor.
     * @param Context\FrontendContext $context
     * @param Core\View\Block\BlockInterface $block
     */
    public function __construct(
        Context\FrontendContext $context,
        Core\View\Block\BlockInterface $block
    ) {
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
