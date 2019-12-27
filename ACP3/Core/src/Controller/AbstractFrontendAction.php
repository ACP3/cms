<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;
use ACP3\Core\Controller\Event\CustomTemplateVariableEvent;

abstract class AbstractFrontendAction extends Core\Controller\AbstractWidgetAction
{
    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
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
     * @var Core\Helpers\RedirectMessages
     */
    private $redirectMessages;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirectResponse;
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

        $this->assets = $context->getAssets();
        $this->breadcrumb = $context->getBreadcrumb();
        $this->title = $context->getTitle();
        $this->actionHelper = $context->getActionHelper();
        $this->redirectMessages = $context->getRedirectMessagesHelper();
        $this->redirectResponse = $context->getRedirectResponse();
    }

    /**
     * Helper function for initializing models, etc.
     *
     * @throws \ACP3\Core\ACL\Exception\AccessForbiddenException
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
    }

    /**
     * {@inheritdoc}
     */
    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('BREADCRUMB', $this->breadcrumb->getBreadcrumb());
        $this->view->assign('LAYOUT', $this->fetchLayoutViaInheritance());

        $this->eventDispatcher->dispatch(
            new CustomTemplateVariableEvent($this->view),
            CustomTemplateVariableEvent::NAME
        );
    }

    protected function fetchLayoutViaInheritance(): string
    {
        if ($this->request->isXmlHttpRequest()) {
            $paths = $this->fetchLayoutPaths('layout.ajax', 'System/layout.ajax.tpl');
        } else {
            $paths = $this->fetchLayoutPaths('layout', 'layout.tpl');
        }

        $this->iterateOverLayoutPaths($paths);

        return $this->getLayout();
    }

    private function fetchLayoutPaths(string $layoutFileName, string $defaultLayoutName): array
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
     * @param string[] $paths
     */
    private function iterateOverLayoutPaths(array $paths): void
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
     * @return $this
     */
    public function setLayout(string $layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return Core\Helpers\RedirectMessages
     */
    public function redirectMessages()
    {
        return $this->redirectMessages;
    }

    /**
     * @return \ACP3\Core\Http\RedirectResponse
     */
    public function redirect()
    {
        return $this->redirectResponse;
    }
}
