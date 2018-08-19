<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Menus\Core\Breadcrumb;

use ACP3\Core;
use ACP3\Core\Breadcrumb\Event\StepsBuildCacheEvent;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Menus;
use Psr\Container\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Steps extends Core\Breadcrumb\Steps
{
    /**
     * @var \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository
     */
    protected $menuItemRepository;
    /**
     * @var array
     */
    protected $stepsFromDb = [];

    /**
     * Breadcrumb constructor.
     *
     * @param \Psr\Container\ContainerInterface                            $container
     * @param \ACP3\Core\I18n\Translator                                   $translator
     * @param \ACP3\Core\Http\RequestInterface                             $request
     * @param \ACP3\Core\Router\RouterInterface                            $router
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface  $eventDispatcher
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository $menuItemRepository
     */
    public function __construct(
        ContainerInterface $container,
        Core\I18n\Translator $translator,
        RequestInterface $request,
        Core\Router\RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository
    ) {
        parent::__construct($container, $translator, $request, $router, $eventDispatcher);

        $this->menuItemRepository = $menuItemRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function replaceAncestor(string $title, string $path = '', bool $dbSteps = false): Core\Breadcrumb\Steps
    {
        if ($dbSteps === true) {
            \end($this->stepsFromDb);
            $this->stepsFromDb[(int) \key($this->stepsFromDb)] = $this->buildStepItem($title, $path);
        }

        return parent::replaceAncestor($title, $path, $dbSteps);
    }

    /**
     * {@inheritdoc}
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    protected function buildBreadcrumbCacheForFrontend(): void
    {
        parent::buildBreadcrumbCacheForFrontend();

        if (empty($this->stepsFromDb)) {
            $this->prePopulate();
        }

        $this->eventDispatcher->dispatch(
            'menus.breadcrumb.steps.build_frontend_cache_after',
            new StepsBuildCacheEvent($this)
        );

        if (!empty($this->stepsFromDb)) {
            $offset = $this->findFirstMatchingStep();

            $this->breadcrumbCache = \array_merge($this->stepsFromDb, \array_slice($this->steps, $offset));
        }
    }

    /**
     * Initializes and pre populates the breadcrumb.
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    private function prePopulate(): void
    {
        $items = $this->menuItemRepository->getMenuItemsByUri($this->getPossiblyMatchingRoutes());

        $matches = $this->findRestrictionInRoutes($items);
        if (!empty($matches)) {
            list($leftId, $rightId) = $matches;

            foreach ($items as $item) {
                if ($item['left_id'] <= $leftId && $item['right_id'] >= $rightId) {
                    $this->appendFromDB($item['title'], $item['uri']);
                }
            }
        }
    }

    /**
     * @return array
     */
    private function getPossiblyMatchingRoutes(): array
    {
        return [
            $this->request->getQuery(),
            $this->request->getUriWithoutPages(),
            $this->request->getFullPath(),
            $this->request->getModuleAndController(),
            $this->request->getModule(),
        ];
    }

    /**
     * @param array $items
     *
     * @return array
     */
    private function findRestrictionInRoutes(array $items): array
    {
        \rsort($items);
        foreach ($items as $index => $item) {
            if (\in_array($item['uri'], $this->getPossiblyMatchingRoutes())) {
                return [
                    $item['left_id'],
                    $item['right_id'],
                ];
            }
        }

        return [];
    }

    /**
     * Zuweisung einer neuen Stufe zur Brotkrümelspur.
     *
     * @param string $title
     * @param string $path
     *
     * @return $this
     */
    private function appendFromDB(string $title, string $path = ''): self
    {
        $this->stepsFromDb[] = $this->buildStepItem($title, $path);

        return $this;
    }

    /**
     * @return int
     */
    private function findFirstMatchingStep(): int
    {
        $steps = \array_reverse($this->steps);
        $lastDbStep = \end($this->stepsFromDb);

        $matched = false;
        $offset = 0;
        foreach ($steps as $index => $step) {
            if ($step['uri'] === $lastDbStep['uri']) {
                $matched = true;
                $offset = $index;

                break;
            }
        }

        return $this->hasUseIndex($matched, $lastDbStep['uri']) ? \count($steps) - $offset : 0;
    }

    /**
     * @param bool   $matched
     * @param string $uri
     *
     * @return bool
     */
    private function hasUseIndex(bool $matched, string $uri): bool
    {
        return $matched === true || $uri === $this->router->route($this->request->getQuery());
    }

    /**
     * {@inheritdoc}
     */
    public function removeByPath(string $path): Core\Breadcrumb\Steps
    {
        parent::removeByPath($path);

        $path = $this->router->route($path);

        $this->stepsFromDb = \array_filter(
            $this->stepsFromDb,
            function (array $step) use ($path) {
                return $step['uri'] !== $path;
            }
        );

        return $this;
    }
}
