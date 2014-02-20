<?php

namespace ACP3\Core\Modules;

use ACP3\Core;

/**
 * Module controller
 *
 * @author Tino Goratsch
 */
abstract class Controller
{

    /**
     *
     * @var \ACP3\Core\Auth
     */
    protected $auth;

    /**
     *
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;

    /**
     *
     * @var \ACP3\Core\Date
     */
    protected $date;

    /**
     *
     * @var \Doctrine\DBAL\Connection
     */
    protected $db;

    /**
     *
     * @var \ACP3\Core\Lang
     */
    protected $lang;

    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;

    /**
     *
     * @var \ACP3\Core\Session
     */
    protected $session;

    /**
     *
     * @var \ACP3\Core\URI
     */
    protected $uri;

    /**
     *
     * @var \ACP3\Core\View
     */
    protected $view;

    public function __construct(
        Core\Auth $auth,
        Core\Breadcrumb $breadcrumb,
        Core\Date $date,
        \Doctrine\DBAL\Connection $db,
        Core\Lang $lang,
        Core\Session $session,
        Core\URI $uri,
        Core\View $view,
        Core\SEO $seo)
    {
        $this->auth = $auth;
        $this->breadcrumb = $breadcrumb;
        $this->date = $date;
        $this->db = $db;
        $this->lang = $lang;
        $this->session = $session;
        $this->uri = $uri;
        $this->view = $view;
        $this->seo = $seo;
    }

    public function display()
    {
        $view = $this->view;
        // Content-Template automatisch setzen
        if ($view->getContentTemplate() === '') {
            $view->setContentTemplate($this->uri->mod . '/' . $this->uri->file . '.tpl');
        }

        if ($view->getNoOutput() === false) {
            if ($view->getContent() === '') {
                $view->setContent($view->fetchTemplate($view->getContentTemplate()));
            }

            // Evtl. gesetzten Content-Type des Servers überschreiben
            header($view->getContentType());

            if ($view->getLayout() !== '') {
                $view->assign('PAGE_TITLE', CONFIG_SEO_TITLE);
                $view->assign('HEAD_TITLE', $this->breadcrumb->output(3));
                $view->assign('TITLE', $this->breadcrumb->output(2));
                $view->assign('BREADCRUMB', $this->breadcrumb->output());
                $view->assign('META', $this->seo->getMetaTags());
                $view->assign('CONTENT', $view->getContent() . $view->getContentAppend());

                $minify = $view->buildMinifyLink();
                $file = $view->getLayout();
                $layout = substr($file, 0, strpos($file, '.'));
                $view->assign('MIN_STYLESHEET', sprintf($minify, 'css') . ($layout !== 'layout' ? '&amp;layout=' . $layout : ''));
                $view->assign('MIN_JAVASCRIPT', sprintf($minify, 'js'));

                $view->displayTemplate($file);
            } else {
                echo $view->getContent();
            }
        }
    }

}
