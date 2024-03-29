<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\ViewProviders;

use ACP3\Core\Breadcrumb\Steps;
use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\View;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;
use ACP3\Modules\ACP3\Files\Repository\FilesRepository;

class FileDetailsViewProvider
{
    public function __construct(private readonly Steps $breadcrumb, private readonly FilesRepository $filesRepository, private readonly CategoryRepository $categoryRepository, private readonly RequestInterface $request, private readonly SettingsInterface $settings, private readonly Title $title, private readonly Translator $translator, private readonly View $view)
    {
    }

    /**
     * @return array<string, mixed>
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function __invoke(int $fileId): array
    {
        $file = $this->filesRepository->getOneById($fileId);

        $this->addBreadcrumbSteps($file, $file['category_id']);

        $settings = $this->settings->getSettings(FilesSchema::MODULE_NAME);
        $file['text'] = $this->view->fetchStringAsTemplate($file['text']);

        return [
            'file' => $file,
            'dateformat' => $settings['dateformat'],
        ];
    }

    /**
     * @param array<string, mixed> $file
     *
     * @throws \Doctrine\DBAL\Exception
     */
    private function addBreadcrumbSteps(array $file, int $categoryId): void
    {
        $this->breadcrumb->append($this->translator->t('files', 'files'), 'files');

        foreach ($this->categoryRepository->fetchNodeWithParents($categoryId) as $category) {
            $this->breadcrumb->append($category['title'], 'files/index/files/cat_' . $category['id']);
        }

        $this->breadcrumb->append(
            $file['title'],
            $this->request->getQuery()
        );
        $this->title->setPageTitle($file['title']);
    }
}
