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
     * @var Core\Assets
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
     * Das zuverwendende Seitenlayout
     *
     * @var string
     */
    protected $layout = 'layout.tpl';

    /**
     * @param Core\Context\Frontend $frontendContext
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
        // Aktuelle Datensatzposition bestimmen
        if (!defined('POS')) {
            define('POS', (int)$this->request->page >= 1 ? (int)($this->request->page - 1) * $this->auth->entries : 0);
        }

        $path = $this->request->area . '/' . $this->request->mod . '/' . $this->request->controller . '/' . $this->request->file;

        if ($this->acl->hasPermission($path) === false) {
            throw new Core\Exceptions\UnauthorizedAccess();
        }

        $this->view->assign('PHP_SELF', PHP_SELF);
        $this->view->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
        $this->view->assign('ROOT_DIR', ROOT_DIR);
        $this->view->assign('ROOT_DIR_ABSOLUTE', ROOT_DIR_ABSOLUTE);
        $this->view->assign('HOST_NAME', HOST_NAME);
        $this->view->assign('DESIGN_PATH', DESIGN_PATH);
        $this->view->assign('DESIGN_PATH_ABSOLUTE', DESIGN_PATH_ABSOLUTE);
        $this->view->assign('UA_IS_MOBILE', Core\Functions::isMobileBrowser());
        $this->view->assign('IN_ADM', $this->request->area === 'admin');

        $this->view->assign('LANG_DIRECTION', $this->lang->getDirection());
        $this->view->assign('LANG', $this->lang->getLanguage2Characters());

        return parent::preDispatch();
    }

    /**
     * Outputs the requested module controller action
     */
    public function display()
    {
        if ($this->getNoOutput() === false) {
            // Content-Template automatisch setzen
            if ($this->getContentTemplate() === '') {
                $this->setContentTemplate($this->request->mod . '/' . $this->request->controller . '.' . $this->request->file . '.tpl');
            }

            if ($this->getContent() === '') {
                $this->setContent($this->view->fetchTemplate($this->getContentTemplate()));
            }

            // Evtl. gesetzten Content-Type des Servers überschreiben
            header($this->getContentType());

            if ($this->getLayout() !== '') {
                $this->view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
                $this->view->assign('HEAD_TITLE', $this->breadcrumb->output(3));
                $this->view->assign('TITLE', $this->breadcrumb->output(2));
                $this->view->assign('BREADCRUMB', $this->breadcrumb->output());
                $this->view->assign('META', $this->seo->getMetaTags());
                $this->view->assign('CONTENT', $this->getContent() . $this->getContentAppend());

                if ($this->request->getIsAjax() === true) {
                    if ($this->layout !== 'layout.tpl') {
                        $file = $this->layout;
                    } else {
                        $file = 'system/ajax.tpl';
                    }
                } else {
                    $file = $this->layout;
                    $this->view->assign('MIN_STYLESHEET', $this->assets->buildMinifyLink('css', substr($file, 0, strpos($file, '.'))));
                    $this->view->assign('MIN_JAVASCRIPT', $this->assets->buildMinifyLink('js'));
                }

                $this->view->displayTemplate($file);
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