<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;

abstract class AbstractFrontendAction extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Breadcrumb\Steps
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\Breadcrumb\Title
     */
    protected $title;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    protected $actionHelper;
    /**
     * @var string
     */
    private $layout = 'layout.tpl';

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     */
    public function __construct(Context\FrontendContext $context)
    {
        parent::__construct($context);

        $this->breadcrumb = $context->getBreadcrumb();
        $this->title = $context->getTitle();
    }

    /**
     * Helper function for initializing models, etc.
     *
     * @return $this
     */
    public function preDispatch()
    {
        parent::preDispatch();

        $this->view->assign([
            'REQUEST_URI' => $this->request->getServer()->get('REQUEST_URI'),
            'UA_IS_MOBILE' => $this->request->getUserAgent()->isMobileBrowser(),
            'IN_ADM' => $this->request->getArea() === AreaEnum::AREA_ADMIN,
            'IS_HOMEPAGE' => $this->request->isHomepage(),
            'IS_AJAX' => $this->request->isXmlHttpRequest(),
        ]);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('BREADCRUMB', $this->breadcrumb->getBreadcrumb());
        $this->view->assign('LAYOUT', $this->fetchLayoutViaInheritance());

        $this->eventDispatcher->dispatch(
            'core.controller.custom_template_variable',
            new Core\Controller\Event\CustomTemplateVariableEvent($this->view)
        );
    }

    /**
     * @return string
     */
    protected function fetchLayoutViaInheritance()
    {
        if ($this->request->isXmlHttpRequest()) {
            $paths = $this->fetchLayoutPaths('layout.ajax', 'System/layout.ajax.tpl');
        } else {
            $paths = $this->fetchLayoutPaths('layout', 'layout.tpl');
        }

        $this->iterateOverLayoutPaths($paths);

        return $this->getLayout();
    }

    /**
     * @param string $layoutFileName
     * @param string $defaultLayoutName
     *
     * @return array
     */
    private function fetchLayoutPaths($layoutFileName, $defaultLayoutName)
    {
        return [
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.' . $this->request->getController() . '.' . $this->request->getAction() . '.tpl',
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.' . $this->request->getController() . '.tpl',
            $this->request->getModule() . '/' . $this->request->getArea() . '/' . $layoutFileName . '.tpl',
            $this->request->getModule() . '/' . $layoutFileName . '.tpl',
            $defaultLayoutName,
        ];
    }

    /**
     * @param $paths
     */
    private function iterateOverLayoutPaths($paths)
    {
        if ($this->getLayout() !== 'layout.tpl') {
            return;
        }

        foreach ($paths as $path) {
            if ($this->view->templateExists($path)) {
                $this->setLayout($path);

                break;
            }
        }
    }

    /**
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * @param string $layout
     *
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }
}
