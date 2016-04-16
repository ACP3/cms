<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Steps
 * @package ACP3\Core\Breadcrumb
 */
class Steps
{
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
    /**
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var array
     */
    protected $steps = [];
    /**
     * @var array
     */
    protected $breadcrumbCache = [];

    /**
     * Breadcrumb constructor.
     *
     * @param \Symfony\Component\DependencyInjection\ContainerInterface $container
     * @param \ACP3\Core\I18n\Translator $translator
     * @param \ACP3\Core\Http\RequestInterface $request
     * @param \ACP3\Core\RouterInterface $router
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ContainerInterface $container,
        Translator $translator,
        RequestInterface $request,
        RouterInterface $router,
        EventDispatcherInterface $eventDispatcher
    ) {
        $this->container = $container;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * Returns the breadcrumb
     *
     * @return array
     */
    public function getBreadcrumb()
    {
        if (empty($this->breadcrumbCache)) {
            $this->buildBreadcrumbCache();
        }

        return $this->breadcrumbCache;
    }

    /**
     * Sets the breadcrumb cache for the current request
     */
    private function buildBreadcrumbCache()
    {
        $this->eventDispatcher->dispatch(
            'core.breadcrumb.steps.build_cache',
            new StepsBuildCacheEvent($this)
        );

        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $this->buildBreadcrumbCacheForAdmin();
        } else {
            $this->buildBreadcrumbCacheForFrontend();
        }

        $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['last'] = true;
    }

    /**
     * Sets the breadcrumb steps cache for admin panel action requests
     */
    private function buildBreadcrumbCacheForAdmin()
    {
        if (empty($this->steps)) {
            $this->eventDispatcher->dispatch(
                'core.breadcrumb.steps.build_admin_cache_empty_steps_before',
                new StepsBuildCacheEvent($this)
            );

            $this->append(
                $this->translator->t($this->request->getModule(), $this->request->getModule()),
                'acp/' . $this->request->getModule()
            );

            $this->appendControllerActionBreadcrumbs();
        } else {
            $this->prepend(
                $this->translator->t($this->request->getModule(), $this->request->getModule()),
                'acp/' . $this->request->getModule()
            );

            $this->eventDispatcher->dispatch(
                'core.breadcrumb.steps.build_admin_cache_not_empty_steps_after',
                new StepsBuildCacheEvent($this)
            );
        }

        $this->breadcrumbCache = $this->steps;
    }

    private function appendControllerActionBreadcrumbs()
    {
        $serviceId = $this->getControllerServiceId();
        if ($this->request->getController() !== 'index' && $this->container->has($serviceId)) {
            $this->append(
                $this->translator->t($this->request->getModule(), $this->getControllerIndexActionTitle()),
                $this->request->getModuleAndController()
            );
        }
        if ($this->request->getAction() !== 'index') {
            $this->append(
                $this->translator->t($this->request->getModule(), $this->getControllerActionTitle()),
                $this->request->getFullPath()
            );
        }
    }

    /**
     * @return string
     */
    private function getControllerServiceId()
    {
        return $this->request->getModule()
        . '.controller.'
        . $this->request->getArea() . '.'
        . $this->request->getController()
        . '.index';
    }

    /**
     * @return string
     */
    private function getControllerActionTitle()
    {
        return $this->request->getArea() . '_' . $this->request->getController() . '_' . $this->request->getAction();
    }

    /**
     * @return string
     */
    private function getControllerIndexActionTitle()
    {
        return $this->request->getArea() . '_' . $this->request->getController() . '_index';
    }

    /**
     * Sets the breadcrumb steps cache for frontend action requests
     */
    protected function buildBreadcrumbCacheForFrontend()
    {
        if (empty($this->steps)) {
            $this->append(
                $this->translator->t($this->request->getModule(), $this->request->getModule()),
                $this->request->getModule()
            );

            $this->appendControllerActionBreadcrumbs();
        }

        $this->breadcrumbCache = $this->steps;
    }

    /**
     * Ersetzt die aktuell letzte Brotkrume mit neuen Werten
     *
     * @param string $title
     * @param string $path
     * @param bool $dbSteps
     *
     * @return $this
     */
    public function replaceAncestor($title, $path = '', $dbSteps = false)
    {
        if ($dbSteps === false) {
            end($this->steps);
            $this->steps[key($this->steps)] = $this->buildStepItem($title, $path);
        }

        return $this;
    }

    /**
     * @param string $title
     * @param string $path
     * @return array
     */
    protected function buildStepItem($title, $path)
    {
        return [
            'title' => $title,
            'uri' => !empty($path) ? $this->router->route($path) : ''
        ];
    }

    /**
     * Zuweisung einer neuen Stufe zur Brotkrümelspur
     *
     * @param string $title
     * @param string $path
     *
     * @return $this
     */
    public function append($title, $path = '')
    {
        if (!$this->stepAlreadyExists($path)) {
            $this->steps[] = $this->buildStepItem($title, $path);
        }

        return $this;
    }

    /**
     * Fügt Brotkrumen an den Anfang an
     *
     * @param string $title
     * @param string $path
     *
     * @return $this
     */
    public function prepend($title, $path)
    {
        if (!$this->stepAlreadyExists($path)) {
            $step = $this->buildStepItem($title, $path);
            array_unshift($this->steps, $step);
        }

        return $this;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function stepAlreadyExists($path)
    {
        $route = $this->router->route($path);
        foreach ($this->steps as $step) {
            if ($step['uri'] === $route) {
                return true;
            }
        }

        return false;
    }
}
