<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


use ACP3\Core\Logger;
use ACP3\Core\Model\Event\ModelSaveEvent;
use ACP3\Modules\ACP3\Seo\Exception\SitemapGenerationException;
use ACP3\Modules\ACP3\Seo\Model\SitemapGenerationModel;
use ACP3\Modules\ACP3\Seo\Utility\SitemapAvailabilityRegistrar;

class OnModelAfterSaveListener
{
    /**
     * @var SitemapAvailabilityRegistrar
     */
    private $sitemapRegistrar;
    /**
     * @var SitemapGenerationModel
     */
    private $sitemapGenerationModel;
    /**
     * @var Logger
     */
    private $logger;

    /**
     * OnModelSaveAfterListener constructor.
     * @param Logger $logger
     * @param SitemapAvailabilityRegistrar $sitemapRegistrar
     * @param SitemapGenerationModel $sitemapGenerationModel
     */
    public function __construct(
        Logger $logger,
        SitemapAvailabilityRegistrar $sitemapRegistrar,
        SitemapGenerationModel $sitemapGenerationModel
    ) {
        $this->logger = $logger;
        $this->sitemapRegistrar = $sitemapRegistrar;
        $this->sitemapGenerationModel = $sitemapGenerationModel;
    }

    /**
     * @param ModelSaveEvent $event
     */
    public function generateSeoSitemap(ModelSaveEvent $event)
    {
        if (array_key_exists($event->getModuleName(), $this->sitemapRegistrar->getAvailableModules())) {
            try {
                $this->sitemapGenerationModel->save();
            } catch (SitemapGenerationException $e) {
                $this->logger->info('seo-sitemap', $e->getMessage());
            }
        }
    }
}
