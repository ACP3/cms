<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema;

class RootCategoriesListViewProvider
{
    public function __construct(private CategoryRepository $categoryRepository)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        return [
            'categories' => $this->categoryRepository->getAllRootCategoriesByModuleName(Schema::MODULE_NAME),
        ];
    }
}
