<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\EventListener;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Core\View;
use ACP3\Core\View\Event\TemplateEvent;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class OnLayoutMetaListener implements EventSubscriberInterface
{
    public function __construct(private readonly View $view, private readonly MetaStatementsServiceInterface $metaStatements, private readonly ApplicationPath $applicationPath, private readonly RequestInterface $request, private readonly RouterInterface $router, private readonly Aliases $aliases)
    {
    }

    public function __invoke(TemplateEvent $event): void
    {
        $this->setCanonicalForExistingUriAlias();
        $this->setCanonicalForHomepage();

        $this->view->assign('META', $this->metaStatements->getMetaTags());

        $event->addContent($this->view->fetchTemplate('Seo/Partials/meta.tpl'));
    }

    /**
     * If there is an URI alias available, set the alias as the canonical URI.
     */
    private function setCanonicalForExistingUriAlias(): void
    {
        if ($this->isInFrontend() && $this->uriAliasExists()) {
            $this->metaStatements->setCanonicalUri($this->router->route($this->request->getQuery(), true));
        }
    }

    private function isInFrontend(): bool
    {
        return $this->request->getArea() === AreaEnum::AREA_FRONTEND;
    }

    private function uriAliasExists(): bool
    {
        return $this->aliases->uriAliasExists($this->request->getQuery()) === true
            && $this->request->getPathInfo() !== $this->aliases->getUriAlias($this->request->getQuery()) . '/';
    }

    /**
     * If we are currently displaying the homepage, set the canonical URL to the website root.
     */
    private function setCanonicalForHomepage(): void
    {
        if ($this->request->isHomepage()) {
            $this->metaStatements->setCanonicalUri($this->request->getSymfonyRequest()->getSchemeAndHttpHost() . $this->applicationPath->getWebRoot());
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'core.layout.meta' => '__invoke',
        ];
    }
}
