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
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class FilesByCategoryIdViewProvider
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
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
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;

    public function __construct(
        Steps $breadcrumb,
        CategoryRepository $categoryRepository,
        Date $date,
        FilesRepository $filesRepository,
        SettingsInterface $settings,
        Translator $translator
    ) {
        $this->breadcrumb = $breadcrumb;
        $this->categoryRepository = $categoryRepository;
        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->settings = $settings;
        $this->translator = $translator;
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
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
     * @throws \Doctrine\DBAL\DBALException
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
