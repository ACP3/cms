<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;
use ACP3\Modules\ACP3\Files\Repository\FilesRepository;

class FilesWidgetViewProvider
{
    public function __construct(private readonly CategoryRepository $categoryRepository, private readonly Date $date, private readonly FilesRepository $filesRepository, private readonly SettingsInterface $settings)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(?int $categoryId, ?int $limit): array
    {
        return [
            'category' => $categoryId ? $this->categoryRepository->getOneById($categoryId) : [],
            'sidebar_files' => $this->fetchFiles($categoryId, $limit),
        ];
    }

    /**
     * @return array<array<string, mixed>>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function fetchFiles(?int $categoryId, ?int $limit): array
    {
        $settings = $this->settings->getSettings(FilesSchema::MODULE_NAME);

        if ($categoryId !== null) {
            return $this->filesRepository->getAllByCategoryId(
                $categoryId,
                $this->date->getCurrentDateTime(),
                $limit ?? $settings['sidebar']
            );
        }

        return $this->filesRepository->getAll(
            $this->date->getCurrentDateTime(),
            $limit ?? $settings['sidebar']
        );
    }
}
