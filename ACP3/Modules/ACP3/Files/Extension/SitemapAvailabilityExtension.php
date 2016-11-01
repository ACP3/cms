<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Extension;


use ACP3\Core\Date;
use ACP3\Core\Router\Router;
use ACP3\Modules\ACP3\Categories\Model\Repository\CategoryRepository;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class SitemapAvailabilityExtension extends AbstractSitemapAvailabilityExtension
{
    /**
     * @var Date
     */
    protected $date;
    /**
     * @var FilesRepository
     */
    protected $filesRepository;
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * SitemapAvailabilityExtension constructor.
     * @param Date $date
     * @param Router $router
     * @param FilesRepository $filesRepository
     * @param CategoryRepository $categoryRepository
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Date $date,
        Router $router,
        FilesRepository $filesRepository,
        CategoryRepository $categoryRepository,
        MetaStatements $metaStatements
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

    public function fetchSitemapUrls()
    {
        $this->addUrl('files/index/index');

        foreach ($this->categoryRepository->getAllByModuleName(Schema::MODULE_NAME) as $category) {
            $this->addUrl('files/index/files/cat_' . $category['id']);
        }

        foreach ($this->filesRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl('files/index/details/id_' . $result['id'], $this->date->format($result['updated_at'], 'Y-m-d'));
        }
    }
}
