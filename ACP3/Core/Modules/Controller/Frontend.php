<?php

namespace ACP3\Core\Modules\Controller;

use ACP3\Core;

/**
 * Class Frontend
 * @package ACP3\Core\Modules\Controller
 */
abstract class Frontend extends Core\Modules\Controller
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
     * Der auszugebende Content-Type der Seite
     *
     * @var string
     */
    protected $contentType = 'Content-Type: text/html; charset=UTF-8';

    /**
     * @param \ACP3\Core\Context\Frontend $frontendContext
     */
    public function __construct(Core\Context\Frontend $frontendContext)
    {
        parent::__construct($frontendContext);

        $this->assets = $frontendContext->getAssets();
        $this->breadcrumb = $frontendContext->getBreadcrumb();
        $this->seo = $frontendContext->getSeo();
    }

    /**
     * Helper function for initializing models, etc.
     */
    public function preDispatch()
    {
        $path = $this->request->area . '/' . $this->request->mod . '/' . $this->request->controller . '/' . $this->request->file;

        if ($this->acl->hasPermission($path) === false) {
            throw new Core\Exceptions\AccessForbidden();
        }

        // Get the current resultset position
        if (!defined('POS')) {
            define('POS', (int)$this->request->page >= 1 ? (int)($this->request->page - 1) * $this->auth->entries : 0);
        }

        // Initialize the breadcrumb
        $this->breadcrumb->prePopulate();

        $this->view->assign('PHP_SELF', PHP_SELF);
        $this->view->assign('REQUEST_URI', $this->router->route($this->request->getOriginalQuery()));
        $this->view->assign('ROOT_DIR', ROOT_DIR);
        $this->view->assign('ROOT_DIR_ABSOLUTE', ROOT_DIR_ABSOLUTE);
        $this->view->assign('HOST_NAME', HOST_NAME);
        $this->view->assign('DESIGN_PATH', DESIGN_PATH);
        $this->view->assign('DESIGN_PATH_ABSOLUTE', DESIGN_PATH_ABSOLUTE);
        $this->view->assign('UA_IS_MOBILE', $this->request->isMobileBrowser());
        $this->view->assign('IN_ADM', $this->request->area === 'admin');
        $this->view->assign('IS_HOMEPAGE', $this->request->getIsHomepage());
        $this->view->assign('IS_AJAX', $this->request->getIsAjax());

        $this->view->assign('LANG_DIRECTION', $this->lang->getDirection());
        $this->view->assign('LANG', $this->lang->getLanguage2Characters());

        return parent::preDispatch();
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
                    $this->setTemplate($this->request->mod . '/' . ucfirst($this->request->area) . '/' . $this->request->controller . '.' . $this->request->file . '.tpl');
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
