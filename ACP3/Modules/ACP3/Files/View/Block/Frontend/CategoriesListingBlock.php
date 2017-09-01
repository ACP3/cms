<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Categories\Cache\CategoriesCacheStorage;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class CategoriesListingBlock extends AbstractBlock
{
    /**
     * @var CategoriesCacheStorage
     */
    private $categoriesCache;

    /**
     * CategoriesListingBlock constructor.
     * @param BlockContext $context
     * @param CategoriesCacheStorage $categoriesCache
     */
    public function __construct(BlockContext $context, CategoriesCacheStorage $categoriesCache)
    {
        parent::__construct($context);

        $this->categoriesCache = $categoriesCache;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'categories' => $this->categoriesCache->getCache(Schema::MODULE_NAME)
        ];
    }
}
