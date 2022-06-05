<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\Http\RequestInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Core\View\Event\TemplateEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnLayoutContentAfterRenderStructuredDataListener implements EventSubscriberInterface
{
    public function __construct(private readonly RequestInterface $request, private readonly MetaStatementsServiceInterface $metaStatementsService)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        $structuredData = $this->metaStatementsService->getStructuredData($this->request->getQuery());

        if (!empty($structuredData)) {
            $event->addContent("<script type=\"application/ld+json\">{$structuredData}</script>");
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            'layout.content_after' => '__invoke',
        ];
    }
}
