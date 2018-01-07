<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Core;

use ACP3\Core\Breadcrumb\Title;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Modules\ACP3\Seo\Helper\Enum\IndexPaginatedContentEnum;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;
use ACP3\Modules\ACP3\Seo\Installer\Schema;

class Pagination extends \ACP3\Core\Pagination
{
    /**
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var SettingsInterface
     */
    private $settings;

    /**
     * Pagination constructor.
     *
     * @param Title               $title
     * @param TranslatorInterface $translator
     * @param RequestInterface    $request
     * @param RouterInterface     $router
     * @param MetaStatements      $metaStatements
     * @param SettingsInterface   $settings
     */
    public function __construct(
        Title $title,
        TranslatorInterface $translator,
        RequestInterface $request,
        RouterInterface $router,
        MetaStatements $metaStatements,
        SettingsInterface $settings
    ) {
        parent::__construct($title, $translator, $request, $router);

        $this->metaStatements = $metaStatements;
        $this->settings = $settings;
    }

    /**
     * {@inheritdoc}
     */
    protected function setMetaStatements()
    {
        parent::setMetaStatements();

        if ($this->request->getArea() !== AreaEnum::AREA_ADMIN) {
            if ($this->currentPage - 1 > 0) {
                $this->modifyMetaDescription();
                $this->setMetaPreviousPage();
                $this->preventIndexing();
            }
            $this->setMetaNextPage();
            $this->setMetaCanonicalUri();
        }
    }

    /**
     * @return string
     */
    private function getRoute()
    {
        $path = ($this->request->getArea() === AreaEnum::AREA_ADMIN ? 'acp/' : '') . $this->request->getUriWithoutPages();

        return $this->router->route($path);
    }

    /**
     * Seitenangabe in der Seitenbeschreibung ab Seite 2 angeben.
     */
    private function modifyMetaDescription()
    {
        $this->metaStatements->setDescriptionPostfix(
            $this->translator->t(
                'system',
                'page_x',
                ['%page%' => $this->currentPage]
            )
        );
    }

    private function setMetaPreviousPage()
    {
        $this->metaStatements->setPreviousPage($this->getRoute() . 'page_' . ($this->currentPage - 1) . '/');
    }

    private function preventIndexing()
    {
        $seoSettings = $this->settings->getSettings(Schema::MODULE_NAME);

        if ($seoSettings['index_paginated_content'] === IndexPaginatedContentEnum::INDEX_FIST_PAGE_ONLY) {
            $this->metaStatements->setPageRobotsSettings('noindex,follow');
        }
    }

    private function setMetaNextPage()
    {
        if ($this->currentPage + 1 <= $this->totalPages) {
            $this->metaStatements->setNextPage($this->getRoute() . 'page_' . ($this->currentPage + 1) . '/');
        }
    }

    private function setMetaCanonicalUri()
    {
        if ($this->request->getParameters()->get('page', 0) === 1) {
            $this->metaStatements->setCanonicalUri($this->getRoute());
        }
    }
}
