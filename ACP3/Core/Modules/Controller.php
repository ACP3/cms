<?php

namespace ACP3\Core\Modules;

use ACP3\Core;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class Controller
 * @package ACP3\Core\Modules
 */
abstract class Controller implements ControllerInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    /**
     * @var \ACP3\Core\ACL
     */
    protected $acl;
    /**
     * @var \ACP3\Core\User
     */
    protected $user;
    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
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
     * @var \ACP3\Core\Http\Request
     */
    protected $request;
    /**
     * @var \ACP3\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\Validator\Validator
     */
    protected $validator;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Core\Config
     */
    protected $config;
    /**
     * Nichts ausgeben
     */
    protected $noOutput = false;
    /**
     * Das zu verwendende Template
     *
     * @var string
     */
    protected $template = '';

    /**
     * @param \ACP3\Core\Modules\Controller\Context $context
     */
    public function __construct(Controller\Context $context)
    {
        $this->eventDispatcher = $context->getEventDispatcher();
        $this->acl = $context->getACL();
        $this->user = $context->getUser();
        $this->lang = $context->getLang();
        $this->request = $context->getRequest();
        $this->router = $context->getRouter();
        $this->validator = $context->getValidator();
        $this->view = $context->getView();
        $this->modules = $context->getModules();
        $this->config = $context->getConfig();
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
     * @param string $file
     *
     * @return $this
     */
    public function setTemplate($file)
    {
        $this->template = $file;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function get($serviceId)
    {
        return $this->container->get($serviceId);
    }

    /**
     * @inheritdoc
     */
    public function setContainer(ContainerInterface $container)
    {
        $this->container = $container;

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function display($controllerActionResult)
    {
        if ($this->getNoOutput() === false && $this->getTemplate() !== '') {
            $this->view->displayTemplate($this->getTemplate());
        }
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
     * Setter Methode für die $this->noOutput Variable
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
}
