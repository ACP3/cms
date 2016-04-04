<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Breadcrumb;

use ACP3\Core\Controller\AreaEnum;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\RouterInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;
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
     * @param \ACP3\Core\I18n\Translator                                $translator
     * @param \ACP3\Core\Http\RequestInterface                          $request
     * @param \ACP3\Core\RouterInterface                                $router
     * @param \ACP3\Core\Breadcrumb\Title                               $title
     */
    public function __construct(
        ContainerInterface $container,
        Translator $translator,
        RequestInterface $request,
        RouterInterface $router,
        Title $title
    ) {
        $this->container = $container;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getPageTitleSeparator()
    {
        return $this->title->getPageTitleSeparator();
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPageTitlePostfix($value)
    {
        $this->title->setPageTitlePostfix($value);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setPageTitlePrefix($value)
    {
        $this->title->setPageTitlePrefix($value);

        return $this;
    }

    /**
     * Returns the site title
     *
     * @return string
     */
    public function getSiteTitle()
    {
        return $this->title->getSiteTitle();
    }

    /**
     * Returns the title of the current page
     *
     * @return string
     */
    public function getPageTitle()
    {
        if (empty($this->breadcrumbCache)) {
            $this->setBreadcrumbCache();
        }

        return $this->title->getPageTitle();
    }

    /**
     * Returns the title of the current page + the site title
     *
     * @return string
     */
    public function getSiteAndPageTitle()
    {
        if (empty($this->breadcrumbCache)) {
            $this->setBreadcrumbCache();
        }

        return $this->title->getSiteAndPageTitle();
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
        if ($this->request->getArea() === AreaEnum::AREA_ADMIN) {
            $this->setBreadcrumbCacheForAdmin();
        } else {
            $this->setBreadcrumbCacheForFrontend();
        }

        // Mark the last breadcrumb
        $this->breadcrumbCache[count($this->breadcrumbCache) - 1]['last'] = true;

        $this->title->setPageTitle($this->breadcrumbCache[count($this->breadcrumbCache) - 1]['title']);
    }

    /**
     * Sets the breadcrumb steps cache for admin panel action requests
     */
    private function setBreadcrumbCacheForAdmin()
    {
        if ($this->request->getModule() !== 'acp') {
            // An postfix for the page title has been already set
            if (!empty($this->title->getPageTitlePostfix())) {
                $this->setPageTitlePostfix(
                    $this->title->getPageTitlePostfix()
                    . $this->getPageTitleSeparator()
                    . $this->translator->t('system', 'acp')
                );
            } else {
                $this->setPageTitlePostfix($this->translator->t('system', 'acp'));
            }
        }

        // No breadcrumb has been set yet
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
