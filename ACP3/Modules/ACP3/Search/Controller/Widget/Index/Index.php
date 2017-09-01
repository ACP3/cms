<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Search\Controller\Widget\Index;

use ACP3\Core;

class Index extends Core\Controller\AbstractWidgetAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\View\Block\BlockInterface
     */
    private $block;

    /**
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     * @param Core\View\Block\BlockInterface $block
     */
    public function __construct(
        Core\Controller\Context\WidgetContext $context,
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
        $this->setCacheResponseCacheable();

        return $this->block->render();
    }
}
