<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Filesseo\Extension;

use ACP3\Core\Date;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Modules\ACP3\Categories\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Helpers;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Repository\FilesRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    /**
     * @var Date
     */
    private $date;
    /**
     * @var FilesRepository
     */
    private $filesRepository;
    /**
     * @var CategoryRepository
     */
    private $categoryRepository;

    public function __construct(
        Date $date,
        RouterInterface $router,
        FilesRepository $filesRepository,
        CategoryRepository $categoryRepository,
        MetaStatementsServiceInterface $metaStatements
    ) {
        parent::__construct($router, $metaStatements);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * @return string
     */
    public function getModuleName()
    {
        return Schema::MODULE_NAME;
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\Exception
     */
    public function fetchSitemapUrls($isSecure = null)
    {
        $this->addUrl('files/index/index', null, $isSecure);

        foreach ($this->categoryRepository->getAllByModuleName(Schema::MODULE_NAME) as $category) {
            $this->addUrl('files/index/files/cat_' . $category['id'], null, $isSecure);
        }

        foreach ($this->filesRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl(
                sprintf(Helpers::URL_KEY_PATTERN, $result['id']),
                $this->date->toDateTime($result['updated_at']),
                $isSecure
            );
        }
    }
}
