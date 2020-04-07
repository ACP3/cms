<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Frontend\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Details extends Core\Controller\AbstractFrontendAction
{
    use Core\Cache\CacheResponseTrait;

    /**
     * @var \ACP3\Core\Date
     */
    protected $date;
    /**
     * @var \ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository
     */
    protected $filesRepository;
    /**
     * @var \ACP3\Modules\ACP3\Files\Cache
     */
    protected $filesCache;
    /**
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        Core\Controller\Context\FrontendContext $context,
        Core\Date $date,
        Files\Model\Repository\FilesRepository $filesRepository,
        Files\Cache $filesCache,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->filesCache = $filesCache;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @throws Core\Controller\Exception\ResultNotExistsException
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(int $id): array
    {
        if ($this->filesRepository->resultExists($id, $this->date->getCurrentDateTime()) === true) {
            $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

            $file = $this->filesCache->getCache($id);

            $this->addBreadcrumbSteps($file, $file['category_id']);

            $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);
            $file['text'] = $this->view->fetchStringAsTemplate($file['text']);

            return [
                'file' => $file,
                'dateformat' => $settings['dateformat'],
            ];
        }

        throw new Core\Controller\Exception\ResultNotExistsException();
    }

    /**
     * @throws \Doctrine\DBAL\DBALException
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
