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
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

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
     * @var \ACP3\Modules\ACP3\Seo\Helper\MetaStatements
     */
    protected $metaStatements;
    /**
     * @var ApplicationPath
     */
    private $applicationPath;

    /**
     * OnFrontControllerBeforeDispatchListener constructor.
     *
     * @param ApplicationPath                              $applicationPath
     * @param \ACP3\Core\Http\RequestInterface             $request
     * @param \ACP3\Core\Router\RouterInterface            $router
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases   $aliases
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function __construct(
        ApplicationPath $applicationPath,
        RequestInterface $request,
        RouterInterface $router,
        Aliases $aliases,
        MetaStatements $metaStatements
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->aliases = $aliases;
        $this->metaStatements = $metaStatements;
        $this->applicationPath = $applicationPath;
    }

    /**
     * @param \ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent $event
     */
    public function onBeforeDispatch(ControllerActionBeforeDispatchEvent $event)
    {
        $this->setCanonicalForExistingUriAlias($event);
        $this->setCanonicalForHomepage();
    }

    /**
     * If there is an URI alias available, set the alias as the canonical URI.
     *
     * @param \ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent $event
     */
    private function setCanonicalForExistingUriAlias(ControllerActionBeforeDispatchEvent $event)
    {
        if ($this->isInFrontend($event) && $this->uriAliasExists()) {
            $this->metaStatements->setCanonicalUri($this->router->route($this->request->getQuery()));
        }
    }

    /**
     * @param \ACP3\Core\Application\Event\ControllerActionBeforeDispatchEvent $event
     *
     * @return bool
     */
    private function isInFrontend(ControllerActionBeforeDispatchEvent $event)
    {
        return $event->getControllerArea() === AreaEnum::AREA_FRONTEND
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
            $this->metaStatements->setCanonicalUri($this->applicationPath->getWebRoot());
        }
    }
}
