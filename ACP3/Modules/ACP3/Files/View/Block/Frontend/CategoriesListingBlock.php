<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoriesRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class CategoriesListingBlock extends AbstractBlock
{
    /**
     * @var CategoriesRepository
     */
    private $categoriesRepository;

    /**
     * CategoriesListingBlock constructor.
     * @param BlockContext $context
     * @param CategoriesRepository $categoriesRepository
     */
    public function __construct(BlockContext $context, CategoriesRepository $categoriesRepository)
    {
        parent::__construct($context);

        $this->categoriesRepository = $categoriesRepository;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'categories' => $this->categoriesRepository->getAllRootCategoriesByModuleName(Schema::MODULE_NAME)
        ];
    }
}
