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
     * @var \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository
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
     * @param \ACP3\Modules\ACP3\Menus\Model\MenuItemRepository           $menuItemRepository
     */
    public function __construct(
        ContainerInterface $container,
        Core\I18n\Translator $translator,
        RequestInterface $request,
        Core\RouterInterface $router,
        EventDispatcherInterface $eventDispatcher,
        Menus\Model\MenuItemRepository $menuItemRepository
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
            $cItems = count($items);

            // Populate the breadcrumb with internal pages
            for ($i = 0; $i < $cItems; ++$i) {
                $this->appendFromDB($items[$i]['title'], $items[$i]['uri']);
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
        $this->stepsFromDb[] = [
            'title' => $title,
            'uri' => !empty($path) ? $this->router->route($path) : ''
        ];

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function replaceAncestor($title, $path = '', $dbSteps = false)
    {
        if ($dbSteps === true) {
            $index = count($this->stepsFromDb) - (!empty($this->stepsFromDb) ? 1 : 0);
            $this->stepsFromDb[$index]['title'] = $title;
            $this->stepsFromDb[$index]['uri'] = !empty($path) ? $this->router->route($path) : '';
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
                $cStepsFromModules = count($this->steps);
                for ($i = 1; $i < $cStepsFromModules; ++$i) {
                    $this->breadcrumbCache[] = $this->steps[$i];
                }
            }
        }
    }
}
