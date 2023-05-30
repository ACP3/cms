<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\Breadcrumb\Event\GetSiteAndPageTitleBeforeEvent;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnBreadcrumbTitleGetSiteAndPageTitleBeforeListener implements EventSubscriberInterface
{
    public function __construct(private readonly RequestInterface $request, private readonly MetaStatementsServiceInterface $metaStatements)
    {
    }

    /**
     * If the current page has a custom meta title set, use it (instead of the default one).
     */
    public function __invoke(GetSiteAndPageTitleBeforeEvent $event): void
    {
        $event->getTitle()->setMetaTitle($this->metaStatements->getTitle($this->request->getQuery()));
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'core.breadcrumb.title.get_site_and_page_title_before' => ['__invoke', -255],
        ];
    }
}
