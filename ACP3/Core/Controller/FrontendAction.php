<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;

/**
 * Class FrontendAction
 * @package ACP3\Core\Controller
 */
abstract class FrontendAction extends Core\Controller\WidgetAction
{
    use Core\Controller\DisplayActionTrait;

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
     * @var Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;
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

        $this->assets = $context->getAssets();
        $this->breadcrumb = $context->getBreadcrumb();
        $this->title = $context->getTitle();
        $this->actionHelper = $context->getActionHelper();
        $this->response = $context->getResponse();
    }

    /**
     * Helper function for initializing models, etc.
     *
     * @return $this
     * @throws \ACP3\Core\Exceptions\AccessForbidden
     */
    public function preDispatch()
    {
        $path = $this->request->getArea() . '/' . $this->request->getFullPathWithoutArea();

        if ($this->acl->hasPermission($path) === false) {
            throw new Core\Exceptions\AccessForbidden();
        }

        // Get the current resultset position
        if (!defined('POS')) {
            define('POS', (int)$this->request->getParameters()->get('page') >= 1 ? (int)($this->request->getParameters()->get('page') - 1) * $this->user->getEntriesPerPage() : 0);
        }

        $this->view->assign([
            'PHP_SELF' => $this->appPath->getPhpSelf(),
            'REQUEST_URI' => $this->request->getServer()->get('REQUEST_URI'),
            'ROOT_DIR' => $this->appPath->getWebRoot(),
            'HOST_NAME' => $this->request->getDomain(),
            'ROOT_DIR_ABSOLUTE' => $this->request->getDomain() . $this->appPath->getWebRoot(),
            'DESIGN_PATH' => $this->appPath->getDesignPathWeb(),
            'DESIGN_PATH_ABSOLUTE' => $this->appPath->getDesignPathAbsolute(),
            'UA_IS_MOBILE' => $this->request->getUserAgent()->isMobileBrowser(),
            'IN_ADM' => $this->request->getArea() === AreaEnum::AREA_ADMIN,
            'IS_HOMEPAGE' => $this->request->isHomepage(),
            'IS_AJAX' => $this->request->isAjax(),
            'LANG_DIRECTION' => $this->translator->getDirection(),
            'LANG' => $this->translator->getShortIsoCode(),
        ]);

        return parent::preDispatch();
    }

    /**
     * @inheritdoc
     */
    protected function applyTemplateAutomatically()
    {
        return $this->request->getModule() . '/' . ucfirst($this->request->getArea()) . '/' . $this->request->getController() . '.' . $this->request->getAction() . '.tpl';
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('BREADCRUMB', $this->breadcrumb->getBreadcrumb());
        $this->view->assign('LAYOUT', $this->request->isAjax() ? 'system/ajax.tpl' : $this->getLayout());

        $this->eventDispatcher->dispatch(
            'core.controller.custom_template_variable',
            new Core\Controller\Event\CustomTemplateVariableEvent($this->view)
        );
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
     * @return $this
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;

        return $this;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \ACP3\Core\View
     */
    protected function getView()
    {
        return $this->view;
    }

    /**
     * @return Core\Helpers\RedirectMessages
     */
    public function redirectMessages()
    {
        if (!$this->redirectMessages) {
            $this->redirectMessages = $this->get('core.helpers.redirect');
        }

        return $this->redirectMessages;
    }

    /**
     * @return Core\Redirect
     */
    public function redirect()
    {
        return $this->get('core.redirect');
    }
}
