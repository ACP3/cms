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
    /**
     * @var \ACP3\Modules\ACP3\Categories\Repository\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(): array
    {
        return [
            'categories' => $this->categoryRepository->getAllRootCategoriesByModuleName(Schema::MODULE_NAME),
        ];
    }
}
