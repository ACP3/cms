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
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema as FilesSchema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;

class FileDetailsViewProvider
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    private $breadcrumb;
    /**
     * @var FilesRepository
     */
    private $filesRepository;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Settings\SettingsInterface
     */
    private $settings;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    private $title;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    private $translator;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        Steps $breadcrumb,
        FilesRepository $filesRepository,
        CategoryRepository $categoryRepository,
        RequestInterface $request,
        SettingsInterface $settings,
        Title $title,
        Translator $translator,
        View $view
    ) {
        $this->breadcrumb = $breadcrumb;
        $this->filesRepository = $filesRepository;
        $this->request = $request;
        $this->settings = $settings;
        $this->title = $title;
        $this->translator = $translator;
        $this->view = $view;
        $this->categoryRepository = $categoryRepository;
    }

    /**
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
