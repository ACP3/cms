<?php

namespace ACP3\Core\Modules;

use ACP3\Core;

/**
 * Class Controller
 * @package ACP3\Core\Modules
 */
abstract class Controller
{
    /**
     * @var \ACP3\Core\Auth
     */
    protected $auth;
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
     * @var \ACP3\Core\URI
     */
    protected $uri;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
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

    public function __construct(Core\Context $context)
    {
        $this->auth = $context->getAuth();
        $this->lang = $context->getLang();
        $this->uri = $context->getUri();
        $this->view = $context->getView();
        $this->modules = $context->getModules();
    }

    /**
     * @return $this
     */
    public function preDispatch()
    {
        $this->setNoOutput(false);

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
     * @param $container
     * @return $this
     */
    public function setContainer($container)
    {
        $this->container = $container;

        return $this;
    }

    public function display()
    {
        if ($this->getNoOutput() === false && $this->getLayout() !== '') {
            $this->view->displayTemplate($this->getLayout());
        }
    }

}