<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Steps
{
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @var TranslatorInterface
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router\RouterInterface
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
    private $lastStep = [];
    /**
     * @var array
     */
    protected $breadcrumbCache = [];

    /**
     * Steps constructor.
     * @param ContainerInterface $container
     * @param TranslatorInterface $translator
     * @param RequestInterface $request
     * @param RouterInterface $router
     * @param EventDispatcherInterface $eventDispatcher
     */
    public function __construct(
        ContainerInterface $container,
        TranslatorInterface $translator,
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

        $this->breadcrumbCache[\count($this->breadcrumbCache) - 1]['last'] = true;
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

        $this->doReplaceLastStep();

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

    private function doReplaceLastStep()
    {
        if (!empty($this->lastStep)) {
            \end($this->steps);
            $this->steps[(int)\key($this->steps)] = $this->lastStep;
        }
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

        $this->doReplaceLastStep();

        $this->breadcrumbCache = $this->steps;
    }

    /**
     * Ersetzt die aktuell letzte Brotkrume mit neuen Werten
     *
     * @param string $title
     * @param string $path
     * @return $this
     */
    public function setLastStepReplacement(string $title, string $path = '')
    {
        $this->lastStep = $this->buildStepItem($title, $path);

        return $this;
    }

    /**
     * @param string $title
     * @param string $path
     * @return array
     */
    protected function buildStepItem(string $title, string $path = '')
    {
        return [
            'title' => $title,
            'uri' => !empty($path) ? $this->router->route($path) : '',
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
    public function append(string $title, string $path = '')
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
    public function prepend(string $title, string $path)
    {
        if (!$this->stepAlreadyExists($path)) {
            $step = $this->buildStepItem($title, $path);
            \array_unshift($this->steps, $step);
        }

        return $this;
    }

    /**
     * @param string $path
     *
     * @return bool
     */
    private function stepAlreadyExists(string $path)
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
