<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\View\Block\Frontend;

use ACP3\Core\View\Block\AbstractFormBlock;
use ACP3\Core\View\Block\Context\FormBlockContext;
use ACP3\Modules\ACP3\Search\Helpers;

class SearchFormBlock extends AbstractFormBlock
{
    /**
     * @var Helpers
     */
    private $searchHelpers;

    /**
     * SearchFormBlock constructor.
     *
     * @param FormBlockContext $context
     * @param Helpers          $searchHelpers
     */
    public function __construct(FormBlockContext $context, Helpers $searchHelpers)
    {
        parent::__construct($context);

        $this->searchHelpers = $searchHelpers;
    }

    /**
     * {@inheritdoc}
     */
    public function render()
    {
        $searchAreas = [
            'title_content' => $this->translator->t('search', 'title_and_content'),
            'title' => $this->translator->t('search', 'title_only'),
            'content' => $this->translator->t('search', 'content_only'),
        ];

        $sortDirections = [
            'asc' => $this->translator->t('search', 'asc'),
            'desc' => $this->translator->t('search', 'desc'),
        ];

        return [
            'form' => \array_merge($this->getData(), $this->getRequestData()),
            'search_mods' => $this->searchHelpers->getModules(),
            'search_areas' => $this->forms->checkboxGenerator(
                'area',
                $searchAreas,
                'title_content'
            ),
            'sort_hits' => $this->forms->checkboxGenerator('sort', $sortDirections, 'asc'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultData(): array
    {
        return ['search_term' => ''];
    }
}
