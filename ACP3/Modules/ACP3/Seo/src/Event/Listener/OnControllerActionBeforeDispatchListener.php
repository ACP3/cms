<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;

use ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\SEO\MetaStatementsServiceInterface;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;

class OnControllerActionBeforeDispatchListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Modules\ACP3\Seo\Core\Router\Aliases
     */
    protected $aliases;
    /**
     * @var \ACP3\Core\SEO\MetaStatementsServiceInterface
     */
    protected $metaStatements;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;

    public function __construct(
        ApplicationPath $applicationPath,
        RequestInterface $request,
        RouterInterface $router,
        Aliases $aliases,
        MetaStatementsServiceInterface $metaStatements
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->aliases = $aliases;
        $this->metaStatements = $metaStatements;
        $this->applicationPath = $applicationPath;
    }

    public function __invoke(ControllerActionBeforeDispatchEvent $event)
    {
        $this->setCanonicalForExistingUriAlias($event);
        $this->setCanonicalForHomepage();
    }

    /**
     * If there is an URI alias available, set the alias as the canonical URI.
     */
    private function setCanonicalForExistingUriAlias(ControllerActionBeforeDispatchEvent $event)
    {
        if ($this->isInFrontend($event) && $this->uriAliasExists()) {
            $this->metaStatements->setCanonicalUri($this->router->route($this->request->getQuery(), true));
        }
    }

    /**
     * @return bool
     */
    private function isInFrontend(ControllerActionBeforeDispatchEvent $event)
    {
        return $event->getArea() === AreaEnum::AREA_FRONTEND
        && $this->request->getArea() === AreaEnum::AREA_FRONTEND;
    }

    /**
     * @return bool
     */
    private function uriAliasExists()
    {
        return $this->aliases->uriAliasExists($this->request->getQuery()) === true
        && $this->request->getPathInfo() !== $this->aliases->getUriAlias($this->request->getQuery()) . '/';
    }

    /**
     * If we are currently displaying the homepage, set the canonical URL to the website root.
     */
    private function setCanonicalForHomepage()
    {
        if ($this->request->isHomepage()) {
            $this->metaStatements->setCanonicalUri($this->request->getSymfonyRequest()->getSchemeAndHttpHost() . $this->applicationPath->getWebRoot());
        }
    }
}
