<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Files\Controller\Widget\Index;

use ACP3\Core;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files;
use ACP3\Modules\ACP3\System\Installer\Schema;

class Index extends Core\Controller\AbstractWidgetAction
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
     * @var \ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        Core\Controller\Context\WidgetContext $context,
        Core\Date $date,
        Files\Model\Repository\FilesRepository $filesRepository,
        CategoryRepository $categoryRepository
    ) {
        parent::__construct($context);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @param int|null $limit
     * @param int      $categoryId
     * @param string   $template
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function execute(?int $limit = null, ?int $categoryId = null, string $template = '')
    {
        $this->setCacheResponseCacheable($this->config->getSettings(Schema::MODULE_NAME)['cache_lifetime']);

        $this->setTemplate(\urldecode($template));

        return [
            'category' => $categoryId !== null ? $this->categoryRepository->getOneById($categoryId) : [],
            'sidebar_files' => $this->fetchFiles($categoryId, $limit),
        ];
    }

    /**
     * @param int|null $categoryId
     * @param int|null $limit
     *
     * @return array
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function fetchFiles(?int $categoryId, ?int $limit): array
    {
        $settings = $this->config->getSettings(Files\Installer\Schema::MODULE_NAME);

        if (!empty($categoryId)) {
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
