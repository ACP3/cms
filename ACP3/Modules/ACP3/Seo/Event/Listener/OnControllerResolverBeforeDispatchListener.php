<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Seo\Event\Listener;


use ACP3\Core\Application\Event\FrontControllerDispatchEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\RouterInterface;
use ACP3\Modules\ACP3\Seo\Core\Router\Aliases;
use ACP3\Modules\ACP3\Seo\Helper\MetaStatements;

/**
 * Class OnControllerResolverBeforeDispatchListener
 * @package ACP3\Modules\ACP3\Seo\Event\Listener
 */
class OnControllerResolverBeforeDispatchListener
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
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
     * OnFrontControllerBeforeDispatchListener constructor.
     *
     * @param \ACP3\Core\Http\RequestInterface             $request
     * @param \ACP3\Core\RouterInterface                   $router
     * @param \ACP3\Modules\ACP3\Seo\Core\Router\Aliases   $aliases
     * @param \ACP3\Modules\ACP3\Seo\Helper\MetaStatements $metaStatements
     */
    public function __construct(
        RequestInterface $request,
        RouterInterface $router,
        Aliases $aliases,
        MetaStatements $metaStatements
    ) {
        $this->request = $request;
        $this->router = $router;
        $this->aliases = $aliases;
        $this->metaStatements = $metaStatements;
    }

    /**
     * If there is an URI alias available, set the alias as the canonical URI
     *
     * @param \ACP3\Core\Application\Event\FrontControllerDispatchEvent $event
     */
    public function onFrontControllerBeforeDispatch(FrontControllerDispatchEvent $event)
    {
        if ($this->isInFrontend($event) && $this->uriAliasExists()) {
            $this->metaStatements->setCanonicalUri($this->router->route($this->request->getQuery()));
        }
    }

    /**
     * @param \ACP3\Core\Application\Event\FrontControllerDispatchEvent $event
     *
     * @return bool
     */
    private function isInFrontend(FrontControllerDispatchEvent $event)
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
}
