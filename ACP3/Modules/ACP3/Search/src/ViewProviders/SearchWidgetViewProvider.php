<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\ViewProviders;

use ACP3\Modules\ACP3\Search\Helpers as SearchHelpers;

class SearchWidgetViewProvider
{
    public function __construct(private SearchHelpers $searchHelpers)
    {
    }

    /**
     * @return array<string, mixed>
     */
    public function __invoke(): array
    {
        return [
            'search_mods' => $this->searchHelpers->getModules(),
        ];
    }
}
