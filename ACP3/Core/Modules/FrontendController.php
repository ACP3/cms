<?php

namespace ACP3\Core\Modules;

use ACP3\Core;

/**
 * Class FrontendController
 * @package ACP3\Core\Modules
 */
abstract class FrontendController extends Core\Modules\Controller
{
    use DisplayControllerActionTrait;

    /**
     * @var \ACP3\Core\Assets
     */
    protected $assets;
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;
    /**
     * @var Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;
    /**
     * @var \ACP3\Core\Modules\Helper\Action
     */
    protected $actionHelper;

    /**
     * @param \ACP3\Core\Modules\Controller\FrontendContext $frontendContext
     */
    public function __construct(Controller\FrontendContext $frontendContext)
    {
        parent::__construct($frontendContext);

        $this->assets = $frontendContext->getAssets();
        $this->breadcrumb = $frontendContext->getBreadcrumb();
        $this->seo = $frontendContext->getSeo();
        $this->actionHelper = $frontendContext->getActionHelper();
        $this->response = $frontendContext->getResponse();
    }

    /**
     * Helper function for initializing models, etc.
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

        $this->view->assign('PHP_SELF', $this->appPath->getPhpSelf());
        $this->view->assign('REQUEST_URI', $this->request->getServer()->get('REQUEST_URI'));
        $this->view->assign('ROOT_DIR', $this->appPath->getWebRoot());
        $this->view->assign('HOST_NAME', $this->request->getDomain());
        $this->view->assign('ROOT_DIR_ABSOLUTE', $this->request->getDomain() . $this->appPath->getWebRoot());
        $this->view->assign('DESIGN_PATH', $this->appPath->getDesignPathWeb());
        $this->view->assign('DESIGN_PATH_ABSOLUTE', $this->appPath->getDesignPathAbsolute());
        $this->view->assign('UA_IS_MOBILE', $this->request->getUserAgent()->isMobileBrowser());
        $this->view->assign('IN_ADM', $this->request->getArea() === 'admin');
        $this->view->assign('IS_HOMEPAGE', $this->request->isHomepage());
        $this->view->assign('IS_AJAX', $this->request->isAjax());
        $this->view->assign('LANG_DIRECTION', $this->translator->getDirection());
        $this->view->assign('LANG', $this->translator->getShortIsoCode());

        return parent::preDispatch();
    }

    /**
     * @param callable    $callback
     * @param null|string $path
     */
    protected function handlePostAction(callable $callback, $path = null)
    {
        try {
            $callback();
        } catch (Core\Validation\Exceptions\InvalidFormTokenException $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), $path);
        } catch (Core\Validation\Exceptions\ValidationFailedException $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * @inheritdoc
     */
    protected function applyTemplateAutomatically()
    {
        return $this->request->getModule() . '/' . ucfirst($this->request->getArea()) . '/' . $this->request->getController() . '.' . $this->request->getControllerAction() . '.tpl';
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
        $this->view->assign('BREADCRUMB', $this->breadcrumb->getBreadcrumb());
        $this->view->assign('META', $this->seo->getMetaTags());
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
