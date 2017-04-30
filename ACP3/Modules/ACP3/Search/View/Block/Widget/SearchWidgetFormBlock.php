<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Search\View\Block\Widget;


use ACP3\Core\View\Block\AbstractBlock;
use ACP3\Core\View\Block\Context\BlockContext;
use ACP3\Modules\ACP3\Search\Helpers;

class SearchWidgetFormBlock extends AbstractBlock
{
    /**
     * @var Helpers
     */
    private $searchHelpers;

    /**
     * SearchWidgetFormBlock constructor.
     * @param BlockContext $context
     * @param Helpers $searchHelpers
     */
    public function __construct(BlockContext $context, Helpers $searchHelpers)
    {
        parent::__construct($context);

        $this->searchHelpers = $searchHelpers;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        return [
            'search_mods' => $this->searchHelpers->getModules()
        ];
    }
}
