<?php
/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\View;

abstract class AbstractFrontendAction extends Core\Controller\AbstractWidgetAction
{
    use LayoutAwareControllerTrait;

    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    protected $actionHelper;

    /**
     * @param \ACP3\Core\Controller\Context\FrontendContext $context
     */
    public function __construct(Context\FrontendContext $context)
    {
        parent::__construct($context);

        $this->actionHelper = $context->getActionHelper();
    }

    /**
     * Helper function for initializing models, etc.
     *
     * @return $this
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

        return $this;
    }

    /**
     * @inheritdoc
     */
    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('LAYOUT', $this->fetchLayoutViaInheritance());

        $this->eventDispatcher->dispatch(
            'core.controller.custom_template_variable',
            new Core\Controller\Event\CustomTemplateVariableEvent($this->view)
        );
    }

    /**
     * @return Core\Helpers\RedirectMessages
     */
    public function redirectMessages()
    {
        return $this->get('core.helpers.redirect');
    }

    /**
     * @return \ACP3\Core\Http\RedirectResponse
     */
    public function redirect()
    {
        return $this->get('core.http.redirect_response');
    }

    /**
     * @inheritdoc
     */
    protected function getRequest(): RequestInterface
    {
        return $this->request;
    }

    /**
     * @inheritdoc
     */
    protected function getView(): View
    {
        return $this->view;
    }
}
