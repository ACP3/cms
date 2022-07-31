<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Steps
{
    /**
     * @var array<array<string, mixed>>
     */
    protected array $steps = [];
    /**
     * @var array<array<string, mixed>>
     */
    protected array $breadcrumbCache = [];

    public function __construct(protected ContainerInterface $container, protected Translator $translator, protected RequestInterface $request, protected RouterInterface $router, protected EventDispatcherInterface $eventDispatcher)
    {
    }

    /**
     * Returns the breadcrumb.
     *
     * @return array<array<string, mixed>>
     */
    public function getBreadcrumb(): array
    {
        if (empty($this->breadcrumbCache)) {
            $this->buildBreadcrumbCache();
        }

        return $this->breadcrumbCache;
    }

    /**
     * Sets the breadcrumb cache for the current request.
     */
    private function buildBreadcrumbCache(): void
    {
        $this->eventDispatcher->dispatch(
            new StepsBuildCacheEvent($this),
            'core.breadcrumb.steps.build_cache'
        );

        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $this->buildBreadcrumbCacheForAdmin();
        } else {
            $this->buildBreadcrumbCacheForFrontend();
        }

        $this->breadcrumbCache[\count($this->breadcrumbCache) - 1]['last'] = true;
    }

    /**
     * Sets the breadcrumb steps cache for admin panel action requests.
     */
    private function buildBreadcrumbCacheForAdmin(): void
    {
        if (empty($this->steps)) {
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
        }

        $this->eventDispatcher->dispatch(
            new StepsBuildCacheEvent($this),
            'core.breadcrumb.steps.build_admin_cache_not_empty_steps_after'
        );

        $this->breadcrumbCache = $this->steps;
    }

    private function appendControllerActionBreadcrumbs(): void
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
                $this->request->getPathInfo()
            );
        }
    }

    private function getControllerServiceId(): string
    {
        return $this->request->getModule()
        . '.controller.'
        . $this->request->getArea()->value . '.'
        . $this->request->getController()
        . '.index';
    }

    private function getControllerActionTitle(): string
    {
        return $this->request->getArea()->value . '_' . $this->request->getController() . '_' . $this->request->getAction();
    }

    private function getControllerIndexActionTitle(): string
    {
        return $this->request->getArea()->value . '_' . $this->request->getController() . '_index';
    }

    /**
     * Sets the breadcrumb steps cache for frontend action requests.
     */
    protected function buildBreadcrumbCacheForFrontend(): void
    {
        if (empty($this->steps)) {
            $this->append(
                $this->translator->t($this->request->getModule(), $this->request->getModule()),
                $this->request->getModule()
            );

            $this->appendControllerActionBreadcrumbs();
        }

        $this->eventDispatcher->dispatch(
            new StepsBuildCacheEvent($this),
            'core.breadcrumb.steps.build_frontend_cache_after'
        );

        $this->breadcrumbCache = $this->steps;
    }

    /**
     * Ersetzt die aktuell letzte Brotkrume mit neuen Werten.
     */
    public function replaceAncestor(string $title, string $path = '', bool $dbSteps = false): self
    {
        if ($dbSteps === false) {
            $this->steps[(int) array_key_last($this->steps)] = $this->buildStepItem($title, $path);
        }

        return $this;
    }

    /**
     * @return array{title: string, uri: string}
     */
    protected function buildStepItem(string $title, string $path): array
    {
        return [
            'title' => $title,
            'uri' => !empty($path) ? $this->router->route($path, true) : '',
        ];
    }

    /**
     * Zuweisung einer neuen Stufe zur Brotkrümelspur.
     */
    public function append(string $title, string $path = ''): self
    {
        if (!$this->stepAlreadyExists($path)) {
            $this->steps[] = $this->buildStepItem($title, $path);
        }

        return $this;
    }

    /**
     * Fügt Brotkrumen an den Anfang an.
     */
    public function prepend(string $title, string $path): self
    {
        if (!$this->stepAlreadyExists($path)) {
            $step = $this->buildStepItem($title, $path);
            array_unshift($this->steps, $step);
        }

        return $this;
    }

    public function removeByPath(string $path): self
    {
        $path = $this->router->route($path, true);

        $this->steps = array_filter(
            $this->steps,
            static fn (array $step) => $step['uri'] !== $path
        );

        return $this;
    }

    private function stepAlreadyExists(string $path): bool
    {
        $route = $this->router->route($path, true);
        foreach ($this->steps as $step) {
            if ($step['uri'] === $route) {
                return true;
            }
        }

        return false;
    }
}
