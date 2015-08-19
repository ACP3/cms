<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use ACP3\Core\Modules\Controller\Context;

/**
 * Class FrontendController
 * @package ACP3\Core\Modules
 */
abstract class FrontendController extends Core\Modules\Controller
{
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
     * Der auszugebende Content-Type der Seite
     *
     * @var string
     */
    protected $contentType = 'Content-Type: text/html; charset=UTF-8';

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

        $this->view->assign('PHP_SELF', PHP_SELF);
        $this->view->assign('REQUEST_URI', $this->request->getServer()->get('REQUEST_URI'));
        $this->view->assign('ROOT_DIR', ROOT_DIR);
        $this->view->assign('HOST_NAME', $this->request->getDomain());
        $this->view->assign('ROOT_DIR_ABSOLUTE', $this->request->getDomain() . ROOT_DIR);
        $this->view->assign('DESIGN_PATH', DESIGN_PATH);
        $this->view->assign('DESIGN_PATH_ABSOLUTE', DESIGN_PATH_ABSOLUTE);
        $this->view->assign('UA_IS_MOBILE', $this->request->getUserAgent()->isMobileBrowser());
        $this->view->assign('IN_ADM', $this->request->getArea() === 'admin');
        $this->view->assign('IS_HOMEPAGE', $this->request->isHomepage());
        $this->view->assign('IS_AJAX', $this->request->isAjax());
        $this->view->assign('LANG_DIRECTION', $this->lang->getDirection());
        $this->view->assign('LANG', $this->lang->getShortIsoCode());

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
        } catch (Core\Exceptions\InvalidFormToken $e) {
            $this->redirectMessages()->setMessage(false, $e->getMessage(), $path);
        } catch (Core\Exceptions\ValidationFailed $e) {
            $this->view->assign('error_msg', $this->get('core.helpers.alerts')->errorBox($e->getMessage()));
        }
    }

    /**
     * Outputs the requested module controller action
     */
    public function display()
    {
        // Output content through the controller
        if ($this->getNoOutput() === false) {
            // Evtl. gesetzten Content-Type des Servers überschreiben
            header($this->getContentType());

            if ($this->getContent() == '') {
                // Set the template automatically
                if ($this->getTemplate() === '') {
                    $this->setTemplate($this->request->getModule() . '/' . ucfirst($this->request->getArea()) . '/' . $this->request->getController() . '.' . $this->request->getControllerAction() . '.tpl');
                }

                $this->view->assign('BREADCRUMB', $this->breadcrumb->getBreadcrumb());
                $this->view->assign('META', $this->seo->getMetaTags());

                $this->view->displayTemplate($this->getTemplate());
            } else {
                echo $this->getContent();
            }
        }
    }

    /**
     * Gibt den Content-Type der anzuzeigenden Seiten zurück
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Weist der aktuell auszugebenden Seite den Content-Type zu
     *
     * @param string $data
     *
     * @return $this
     */
    public function setContentType($data)
    {
        $this->contentType = $data;

        return $this;
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
