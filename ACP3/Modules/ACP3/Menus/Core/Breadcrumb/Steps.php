<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Modules\ACP3\Menus\Core\Breadcrumb;

use ACP3\Core;
use ACP3\Core\Http\RequestInterface;
use ACP3\Modules\ACP3\Menus;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Class Steps
 * @package ACP3\Modules\ACP3\Menus\Core\Breadcrumb
 */
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
     * @param \Symfony\Component\DependencyInjection\ContainerInterface   $container
     * @param \ACP3\Core\I18n\Translator                                  $translator
     * @param \ACP3\Core\Http\RequestInterface                            $request
     * @param \ACP3\Core\RouterInterface                                  $router
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $eventDispatcher
     * @param \ACP3\Modules\ACP3\Menus\Model\Repository\MenuItemRepository           $menuItemRepository
     */
    public function __construct(
        ContainerInterface $container,
        Core\I18n\Translator $translator,
        RequestInterface $request,
        Core\RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        Menus\Model\Repository\MenuItemRepository $menuItemRepository
    ) {
        parent::__construct($container, $translator, $request, $router, $eventDispatcher);

        $this->menuItemRepository = $menuItemRepository;

        $this->prePopulate();
    }

    /**
     * Initializes and pre populates the breadcrumb
     */
    public function prePopulate()
    {
        if ($this->request->getArea() !== Core\Controller\AreaEnum::AREA_ADMIN) {
            $in = [
                $this->request->getQuery(),
                $this->request->getUriWithoutPages(),
                $this->request->getFullPath(),
                $this->request->getModuleAndController(),
                $this->request->getModule()
            ];
            $items = $this->menuItemRepository->getMenuItemsByUri($in);
            foreach ($items as $item) {
                $this->appendFromDB($item['title'], $item['uri']);
            }
        }
    }

    /**
     * Zuweisung einer neuen Stufe zur Brotkrümelspur
     *
     * @param string $title
     * @param string $path
     *
     * @return $this
     */
    protected function appendFromDB($title, $path = '')
    {
        $this->stepsFromDb[] = $this->buildStepItem($title, $path);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function replaceAncestor($title, $path = '', $dbSteps = false)
    {
        if ($dbSteps === true) {
            end($this->stepsFromDb);
            $this->stepsFromDb[(int)key($this->stepsFromDb)] = $this->buildStepItem($title, $path);
        }

        return parent::replaceAncestor($title, $path, $dbSteps);
    }

    /**
     * Sets the breadcrumb steps cache for frontend action requests
     */
    protected function buildBreadcrumbCacheForFrontend()
    {
        parent::buildBreadcrumbCacheForFrontend();

        if (!empty($this->stepsFromDb)) {
            $this->breadcrumbCache = $this->stepsFromDb;

            if ($this->breadcrumbCache[count($this->breadcrumbCache) - 1]['uri'] === $this->steps[0]['uri']) {
                $steps = $this->steps;
                unset($steps[0]);
                $this->breadcrumbCache = array_merge($this->breadcrumbCache, $steps);
            }
        }
    }
}
