<?php

namespace ACP3\Installer\Core\Modules;

use ACP3\Core\Redirect;
use ACP3\Core\XML;
use ACP3\Installer\Core\Context;
use Symfony\Component\DependencyInjection\Container;

/**
 * Module Controller of the installer modules
 * @package ACP3\Installer\Core\Modules
 */
class Controller
{

    /**
     * @var Container
     */
    protected $container;
    /**
     * @var \ACP3\Installer\Core\Lang
     */
    protected $lang;
    /**
     * @var \ACP3\Installer\Core\Request
     */
    protected $request;
    /**
     * @var \ACP3\Installer\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;

    /**
     * Nicht ausgeben
     */
    protected $noOutput = false;
    /**
     * Der auszugebende Content-Type der Seite
     *
     * @var string
     */
    protected $contentType = 'Content-Type: text/html; charset=UTF-8';
    /**
     * Das zuverwendende Template
     *
     * @var string
     */
    protected $template = '';
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

    public function __construct(Context $context)
    {
        $this->lang = $context->getLang();
        $this->request = $context->getRequest();
        $this->router = $context->getRouter();
        $this->view = $context->getView();
    }

    public function preDispatch()
    {
        if (!empty($_POST['lang'])) {
            setcookie('ACP3_INSTALLER_LANG', $_POST['lang'], time() + 3600, '/');
            $this->redirect()->temporary($this->request->mod . '/' . $this->request->controller . '/' . $this->request->file);
        }

        if (defined('LANG') === false) {
            if (!empty($_COOKIE['ACP3_INSTALLER_LANG']) && !preg_match('=/=', $_COOKIE['ACP3_INSTALLER_LANG']) &&
                is_file(INSTALLER_MODULES_DIR . 'Install/Languages/' . $_COOKIE['ACP3_INSTALLER_LANG'] . '.xml') === true
            ) {
                define('LANG', $_COOKIE['ACP3_INSTALLER_LANG']);
            } else {
                define('LANG', \ACP3\Core\Lang::parseAcceptLanguage());
            }
        }

        $this->lang->setLanguage(LANG);

        // Einige Template Variablen setzen
        $this->view->assign('LANGUAGES', $this->_languagesDropdown(LANG));
        $this->view->assign('PHP_SELF', PHP_SELF);
        $this->view->assign('REQUEST_URI', htmlentities($_SERVER['REQUEST_URI']));
        $this->view->assign('ROOT_DIR', ROOT_DIR);
        $this->view->assign('INSTALLER_ROOT_DIR', INSTALLER_ROOT_DIR);
        $this->view->assign('DESIGN_PATH', DESIGN_PATH);
        $this->view->assign('UA_IS_MOBILE', $this->request->isMobileBrowser());

        $languageInfo = \ACP3\Core\XML::parseXmlFile(INSTALLER_MODULES_DIR . 'Install/Languages/' . $this->lang->getLanguage() . '.xml', '/language/info');
        $this->view->assign('LANG_DIRECTION', isset($languageInfo['direction']) ? $languageInfo['direction'] : 'ltr');
        $this->view->assign('LANG', $this->lang->getLanguage2Characters());
    }

    /**
     * @return Redirect
     */
    public function redirect()
    {
        return $this->get('core.redirect');
    }

    /**
     * Gets a class from the service container
     *
     * @param $serviceId
     *
     * @return mixed
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * Generiert das Dropdown-Menü mit der zur Verfügung stehenden Installersprachen
     *
     * @param string $selectedLanguage
     *
     * @return array
     */
    private function _languagesDropdown($selectedLanguage)
    {
        // Dropdown-Menü für die Sprachen
        $languages = array();
        $path = INSTALLER_MODULES_DIR . 'Install/Languages/';
        $files = array_diff(scandir($path), array('.', '..'));
        foreach ($files as $row) {
            $langInfo = XML::parseXmlFile($path . $row, '/language/info');
            if (!empty($langInfo)) {
                $languages[] = array(
                    'language' => substr($row, 0, -4),
                    'selected' => $selectedLanguage === substr($row, 0, -4) ? ' selected="selected"' : '',
                    'name' => $langInfo['name']
                );
            }
        }
        return $languages;
    }

    /**
     * @param $container
     *
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * Fügt weitere Daten an den Seiteninhalt an
     *
     * @param string $data
     *
     * @return $this
     */
    public function appendContent($data)
    {
        $this->contentAppend .= $data;

        return $this;
    }

    public function display()
    {
        if ($this->getNoOutput() === false) {
            // Evtl. gesetzten Content-Type des Servers überschreiben
            header($this->getContentType());

            if ($this->getContent() == '') {
                // Template automatisch setzen
                if ($this->getTemplate() === '') {
                    $this->setTemplate($this->request->mod . '/' . $this->request->controller . '.' . $this->request->file . '.tpl');
                }

                $this->view->assign('PAGE_TITLE', $this->lang->t('install', 'acp3_installation'));
                $this->view->assign('TITLE', $this->lang->t($this->request->mod, $this->request->controller . '_' . $this->request->file));
                $this->view->assign('CONTENT', $this->getContentAppend());
                $this->view->assign('IS_AJAX', $this->request->getIsAjax());

                $this->view->displayTemplate($this->getTemplate());
            } else {
                echo $this->getContent();
            }
        }
    }

    /**
     * Gibt das aktuell zugewiesene Template zurück
     *
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Setzt das Template der Seite
     *
     * @param string $template
     *
     * @return $this
     */
    public function setTemplate($template)
    {
        $this->template = $template;

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
     * Setter Methode für die $this->no_output Variable
     *
     * @param boolean $value
     *
     * @return $this
     */
    public function setNoOutput($value)
    {
        $this->noOutput = (bool)$value;

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
     * Weist dem Template den auszugebenden Inhalt zu
     *
     * @param string $data
     *
     * @return $this
     */
    public function setContent($data)
    {
        $this->content = $data;

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
     * Gibt die anzuhängenden Inhalte an den Seiteninhalt zurück
     *
     * @return string
     */
    public function getContentAppend()
    {
        return $this->contentAppend;
    }
}
