<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


use ACP3\Core\Breadcrumb\Event\GetSiteAndPageTitleBeforeEvent;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

class OnBreadcrumbTitleGetSiteAndPageTitleBeforeListener
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var MetaStatements
     */
    private $metaStatements;

    /**
     * OnBreadcrumbTitleGetSiteAndPageTitleBeforeListener constructor.
     * @param RequestInterface $request
     * @param MetaStatements $metaStatements
     */
    public function __construct(RequestInterface $request, MetaStatements $metaStatements)
    {
        $this->request = $request;
        $this->metaStatements = $metaStatements;
    }

    /**
     * If the current page has a custom meta title set, use it (instead of the default one)
     *
     * @param GetSiteAndPageTitleBeforeEvent $event
     */
    public function useMetaTitle(GetSiteAndPageTitleBeforeEvent $event)
    {
        $event->getTitle()->setMetaTitle($this->metaStatements->getTitle($this->request->getQuery()));
    }
}
