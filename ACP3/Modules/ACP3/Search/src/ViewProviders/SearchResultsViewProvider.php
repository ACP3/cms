<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;

class SearchResultsViewProvider
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        RequestInterface $request,
        Steps $breadcrumb,
        Translator $translator
    ) {
        $this->request = $request;
        $this->breadcrumb = $breadcrumb;
        $this->translator = $translator;
    }

    public function __invoke(
        array $searchResults,
        string $searchTerm
    ): array {
        $this->breadcrumb
            ->append($this->translator->t('search', 'search'), 'search')
            ->append(
                $this->translator->t('search', 'search_results'),
                $this->request->getQuery()
            );

        return [
            'results_mods' => $searchResults,
            'search_term' => $searchTerm,
        ];
    }
}
