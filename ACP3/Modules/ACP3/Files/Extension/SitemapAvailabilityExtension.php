<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Files\Extension;


use ACP3\Core\Date;
use ACP3\Core\Router\Router;
use ACP3\Modules\ACP3\Files\Installer\Schema;
use ACP3\Modules\ACP3\Files\Model\Repository\FilesRepository;
use ACP3\Modules\ACP3\Seo\Extension\AbstractSitemapAvailabilityExtension;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

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
     * SitemapAvailabilityExtension constructor.
     * @param Date $date
     * @param Router $router
     * @param FilesRepository $filesRepository
     * @param MetaStatements $metaStatements
     */
    public function __construct(
        Date $date,
        Router $router,
        FilesRepository $filesRepository,
        MetaStatements $metaStatements
    ) {
        parent::__construct($router, $metaStatements);

        $this->date = $date;
        $this->filesRepository = $filesRepository;
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

        foreach ($this->filesRepository->getAll($this->date->getCurrentDateTime()) as $result) {
            $this->addUrl('files/index/details/id_' . $result['id'], $result['start']);
        }
    }
}
