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
     * @var \ACP3\Core\Auth
     */
    protected $auth;
    /**
     * @var \ACP3\Core\Breadcrumb
     */
    protected $breadcrumb;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    protected $container;
    /**
     * @var \ACP3\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;

    /**
     * @var \ACP3\Core\SEO
     */
    protected $seo;

    /**
     * @var \ACP3\Core\URI
     */
    protected $uri;

    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var Core\Helpers\RedirectMessages
     */
    protected $redirectMessages;

    /**
     * Nichts ausgeben
     */
    protected $noOutput = false;

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
     * Das zuverwendende Template für den Contentbereich
     *
     * @var string
     */
    protected $contentTemplate = '';

    /**
     * Der auszugebende Seiteninhalt
     *
     * @var string
     */
    protected $content = '';

    /**
     * @var string
     */
    protected $contentAppend = '';

    public function __construct(
        Core\Context $context,
        Core\Breadcrumb $breadcrumb,
        Core\SEO $seo)
    {
        $this->auth = $context->getAuth();
        $this->lang = $context->getLang();
        $this->uri = $context->getUri();
        $this->view = $context->getView();
        $this->modules = $context->getModules();
        $this->breadcrumb = $breadcrumb;
        $this->seo = $seo;
    }

    /**
     * Helper function for initializing models, etc.
     */
    public function preDispatch()
    {
        // Aktuelle Datensatzposition bestimmen
        if (!defined('POS')) {
            define('POS', (int)$this->uri->page >= 1 ? (int)($this->uri->page - 1) * $this->auth->entries : 0);
        }

        $path = $this->uri->area . '/' . $this->uri->mod . '/' . $this->uri->controller . '/' . $this->uri->file;

        if ($this->modules->hasPermission($path) === false) {
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
        $this->view->assign('IN_ADM', $this->uri->area === 'admin');

        $this->view->assign('LANG_DIRECTION', $this->lang->getDirection());
        $this->view->assign('LANG', $this->lang->getLanguage2Characters());

        return $this;
    }

    /**
     * Setter Methode für die $this->no_output Variable
     *
     * @param boolean $value
     * @return $this
     */
    public function setNoOutput($value)
    {
        $this->noOutput = (bool)$value;

        return $this;
    }

    /**
     * Gibt zurück, ob die Seitenausgabe mit Hilfe der Bootstraping-Klasse
     * erfolgen soll oder die Datei dies selber handelt
     *
     * @return string
     */
    public function getNoOutput()
    {
        return $this->noOutput;
    }

    /**
     * Weist der aktuell auszugebenden Seite den Content-Type zu
     *
     * @param string $data
     * @return $this
     */
    public function setContentType($data)
    {
        $this->contentType = $data;

        return $this;
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
     * Weist der aktuell auszugebenden Seite ein Layout zu
     *
     * @param string $file
     * @return $this
     */
    public function setLayout($file)
    {
        $this->layout = $file;

        return $this;
    }

    /**
     * Gibt das aktuell zugewiesene Layout zurück
     *
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Setzt das Template für den Contentbereich der Seite
     *
     * @param string $file
     * @return $this
     */
    public function setContentTemplate($file)
    {
        $this->contentTemplate = $file;

        return $this;
    }

    /**
     * Gibt das aktuell zugewiesene Template für den Contentbereich zurück
     *
     * @return string
     */
    public function getContentTemplate()
    {
        return $this->contentTemplate;
    }

    /**
     * Weist dem Template den auszugebenden Inhalt zu
     *
     * @param string $data
     * @return $this
     */
    public function setContent($data)
    {
        $this->content = $data;

        return $this;
    }

    /**
     * Fügt weitere Daten an den Seiteninhalt an
     *
     * @param string $data
     * @return $this
     */
    public function appendContent($data)
    {
        $this->contentAppend .= $data;

        return $this;
    }

    /**
     * Gibt den auszugebenden Seiteninhalt zurück
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Gibt die anzuhängenden Inhalte an den Seiteninhalt zurück
     *
     * @return string
     */
    public function getContentAppend()
    {
        return $this->contentAppend;
    }

    /**
     * Outputs the requested module controller action
     */
    public function display()
    {
        if ($this->getNoOutput() === false) {
            // Content-Template automatisch setzen
            if ($this->getContentTemplate() === '') {
                $this->setContentTemplate($this->uri->mod . '/' . $this->uri->controller . '.' . $this->uri->file . '.tpl');
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

                if ($this->uri->getIsAjax() === true) {
                    if ($this->layout !== 'layout.tpl') {
                        $file = $this->layout;
                    } else {
                        $file = 'system/ajax.tpl';
                    }
                } else {
                    $file = $this->layout;
                    $this->view->assign('MIN_STYLESHEET', $this->view->buildMinifyLink('css', substr($file, 0, strpos($file, '.'))));
                    $this->view->assign('MIN_JAVASCRIPT', $this->view->buildMinifyLink('js'));
                }

                $this->view->displayTemplate($file);
            } else {
                echo $this->getContent();
            }
        }
    }

    /**
     * Gets a class from the service container
     *
     * @param $serviceId
     * @return mixed
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @return Core\Helpers\RedirectMessages
     */
    public function redirectMessages()
    {
        if (!$this->redirectMessages) {
            $this->redirectMessages = new Core\Helpers\RedirectMessages($this->uri, $this->view);
        }

        return $this->redirectMessages;
    }

    /**
     * @param $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }
}
