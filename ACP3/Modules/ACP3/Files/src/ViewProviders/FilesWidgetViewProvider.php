<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Core\Date;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class FilesWidgetViewProvider
{
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoryRepository;
    /**
     * @var \ACP3\Core\Date
     */
    private $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    private $filesRepository;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;

    public function __construct(
        CategoryRepository $categoryRepository,
        Date $date,
        FilesRepository $filesRepository,
        SettingsInterface $settings
    ) {
        $this->categoryRepository = $categoryRepository;
        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->settings = $settings;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
     */
    public function __invoke(?int $categoryId, ?int $limit): array
    {
        return [
            'category' => $categoryId ?$this->categoryRepository->getOneById($categoryId) : [],
            'sidebar_files' => $this->fetchFiles($categoryId, $limit),
        ];
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
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
