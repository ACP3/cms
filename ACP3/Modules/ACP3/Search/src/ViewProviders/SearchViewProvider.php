<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Search\Helpers;

class SearchViewProvider
{
    public function __construct(private Forms $formsHelper, private Helpers $searchHelpers, private RequestInterface $request, private Translator $translator)
    {
    }

    public function __invoke(): array
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
            'form' => array_merge(['search_term' => ''], $this->request->getPost()->all()),
            'search_mods' => $this->searchHelpers->getModules(),
            'search_areas' => $this->formsHelper->checkboxGenerator(
                'area',
                $searchAreas,
                'title_content'
            ),
            'sort_hits' => $this->formsHelper->checkboxGenerator('sort', $sortDirections, 'asc'),
        ];
    }
}
