<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Date;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;
use ACP3\Modules\ACP3\Files\Repository\FilesRepository;

class FilesByCategoryIdViewProvider
{
    public function __construct(private Steps $breadcrumb, private CategoryRepository $categoryRepository, private Date $date, private FilesRepository $filesRepository, private SettingsInterface $settings, private Translator $translator)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $categoryId): array
    {
        $this->addBreadcrumbSteps($categoryId);

        $settings = $this->settings->getSettings(FilesSchema::MODULE_NAME);

        return [
            'categories' => $this->categoryRepository->getAllDirectSiblings($categoryId),
            'dateformat' => $settings['dateformat'],
            'files' => $this->filesRepository->getAllByCategoryId($categoryId, $this->date->getCurrentDateTime()),
        ];
    }

    /**
     * @throws \Doctrine\DBAL\Exception
     */
    private function addBreadcrumbSteps(int $categoryId): void
    {
        $this->breadcrumb
            ->append($this->translator->t('files', 'files'), 'files');

        foreach ($this->categoryRepository->fetchNodeWithParents($categoryId) as $category) {
            $this->breadcrumb->append(
                $category['title'],
                'files/index/files/cat_' . $category['id']
            );
        }
    }
}
