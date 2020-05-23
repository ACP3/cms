<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Search\ViewProviders;

use ACP3\Modules\ACP3\Search\Helpers as SearchHelpers;

class SearchWidgetViewProvider
{
    /**
     * @var \ACP3\Core\Modules
     */
    private $searchHelpers;

    public function __construct(SearchHelpers $searchHelpers)
    {
        $this->searchHelpers = $searchHelpers;
    }

    public function __invoke(): array
    {
        return [
            'search_mods' => $this->searchHelpers->getModules(),
        ];
    }
}
