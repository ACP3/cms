<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Newsletter\Controller\Widget\Index;

use ACP3\Core;

class Index extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var Core\View\Block\FormBlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param Core\View\Block\FormBlockInterface $block
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\View\Block\FormBlockInterface $block
    ) {
        parent::__construct($context);

        $this->block = $block;
    }

    /**
     * @param string $template
     *
     * @return array
     */
    public function execute($template = '')
    {
        $this->view->setTemplate($template);

        return $this->block->render();
    }
}
