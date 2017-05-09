<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Users\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Users;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\View\Block\ListingBlockInterface
     */
    private $block;

    /**
     * Index constructor.
     *
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     * @param Core\View\Block\ListingBlockInterface $block
     */
    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\View\Block\ListingBlockInterface $block
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
