<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Breadcrumb\Event\BreadcrumbStepsBuildCacheEvent;
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
     * @param \Symfony\Component\DependencyInjection\ContainerInterface   $container
     * @param \ACP3\Core\I18n\Translator                                  $translator
     * @param \ACP3\Core\Http\RequestInterface                            $request
     * @param \ACP3\Core\RouterInterface                                  $router
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
     * Ersetzt die aktuell letzte Brotkrume mit neuen Werten
     *
     * @param string $title
     * @param string $path
     * @param bool   $dbSteps
     *
     * @return $this
     */
    public function replaceAncestor($title, $path = '', $dbSteps = false)
    {
        if ($dbSteps === false) {
            $index = count($this->steps) - (!empty($this->steps) ? 1 : 0);
            $this->steps[$index]['title'] = $title;
            $this->steps[$index]['uri'] = !empty($path) ? $this->router->route($path) : '';
        }

        return $this;
    }

    /**
     * Returns the breadcrumb
     *
     * @return array
     */
    public function getBreadcrumb()
    {
        if (empty($this->breadcrumbCache)) {
            $this->setBreadcrumbCache();
        }

        return $this->breadcrumbCache;
    }

    /**
     * Sets the breadcrumb cache for the current request
     */
    private function setBreadcrumbCache()
    {
        $this->eventDispatcher->dispatch('core.breadcrumb.steps.build_cache', new BreadcrumbStepsBuildCacheEvent($this));

        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $this->setBreadcrumbCacheForAdmin();
        } else {
            $this->setBreadcrumbCacheForFrontend();
        }

        $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['last'] = true;
    }

    /**
     * Sets the breadcrumb steps cache for admin panel action requests
     */
    private function setBreadcrumbCacheForAdmin()
    {
        // No breadcrumb steps have been set yet
        if (empty($this->steps)) {
            $this->append($this->translator->t('system', 'acp'), 'acp/acp');

            if ($this->request->getModule() !== 'acp') {
                $this->append(
                    $this->translator->t($this->request->getModule(), $this->request->getModule()),
                    'acp/' . $this->request->getModule()
                );

                $this->setControllerActionBreadcrumbs();
            }
        } else { // Prepend breadcrumb steps, if there have already been some steps set
            if ($this->request->getModule() !== 'acp') {
                $this->prepend(
                    $this->translator->t($this->request->getModule(), $this->request->getModule()),
                    'acp/' . $this->request->getModule()
                );
            }

            $this->prepend($this->translator->t('system', 'acp'), 'acp/acp');
        }
        $this->breadcrumbCache = $this->steps;
    }

    private function setControllerActionBreadcrumbs()
    {
        $serviceId = $this->request->getModule() . '.controller.' . $this->request->getArea() . '.' . $this->request->getController() . '.index';
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
    protected function setBreadcrumbCacheForFrontend()
    {
        // No breadcrumb has been set yet
        if (empty($this->steps)) {
            if ($this->request->getModule() !== 'errors') {
                $this->append(
                    $this->translator->t($this->request->getModule(), $this->request->getModule()),
                    $this->request->getModule()
                );
            }

            $this->setControllerActionBreadcrumbs();
        }

        $this->breadcrumbCache = $this->steps;
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
        $this->steps[] = [
            'title' => $title,
            'uri' => !empty($path) ? $this->router->route($path) : ''
        ];

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
    protected function prepend($title, $path)
    {
        $step = [
            'title' => $title,
            'uri' => $this->router->route($path)
        ];
        array_unshift($this->steps, $step);

        return $this;
    }
}
