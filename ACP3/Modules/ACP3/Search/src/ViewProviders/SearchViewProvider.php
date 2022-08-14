<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\ViewProviders;

use ACP3\Core\Helpers\Forms;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Modules\ACP3\Search\Enum\SearchAreaEnum;
use ACP3\Modules\ACP3\Search\Enum\SortDirectionEnum;
use ACP3\Modules\ACP3\Search\Helpers;

class SearchViewProvider
{
    public function __construct(private readonly Forms $formsHelper, private readonly Helpers $searchHelpers, private readonly RequestInterface $request, private readonly Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        $searchAreas = [
            SearchAreaEnum::TITLE_AND_CONTENT->value => $this->translator->t('search', 'title_and_content'),
            SearchAreaEnum::TITLE->value => $this->translator->t('search', 'title_only'),
            SearchAreaEnum::CONTENT->value => $this->translator->t('search', 'content_only'),
        ];

        $sortDirections = [
            SortDirectionEnum::ASC->value => $this->translator->t('search', 'asc'),
            SortDirectionEnum::DESC->value => $this->translator->t('search', 'desc'),
        ];

        $modules = [];
        foreach ($this->searchHelpers->getModules() as $moduleName => $info) {
            $modules[$moduleName] = $this->translator->t($moduleName, $moduleName);
        }

        return [
            'form' => array_merge(['search_term' => ''], $this->request->getPost()->all()),
            'search_mods' => $this->formsHelper->checkboxGenerator('mods', $modules, array_keys($modules)),
            'search_areas' => $this->formsHelper->checkboxGenerator(
                'area',
                $searchAreas,
                'title_content'
            ),
            'sort_hits' => $this->formsHelper->checkboxGenerator('sort', $sortDirections, 'asc'),
        ];
    }
}
