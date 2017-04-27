<?php
/**
 * Copyright (c) by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing
 * details.
 */

namespace ACP3\Modules\ACP3\News\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var Core\View\Block\ListingBlockInterface
     */
    private $block;

    /**
     * Index constructor.
     * @param Core\Controller\Context\FrontendContext $context
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
     * @param int $cat
     *
     * @return array
     */
    public function execute($cat = 0)
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        return $this->block
            ->setData(['category_id' => $cat])
            ->render();
    }
}
