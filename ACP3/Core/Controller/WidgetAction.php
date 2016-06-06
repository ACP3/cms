<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Core\Controller;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class WidgetAction
 * @package ACP3\Core\Controller
 */
abstract class WidgetAction implements ActionInterface
{
    use Core\Controller\DisplayActionTrait;

    /**
     * @var \Symfony\Component\DependencyInjection\ContainerInterface
     */
    protected $container;
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
     * @var \ACP3\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Modules
     */
    protected $modules;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \ACP3\Core\RouterInterface
     */
    protected $router;
    /**
     * @var \ACP3\Core\Validation\Validator
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
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var Response
     */
    protected $response;

    /**
     * Nichts ausgeben
     */
    private $noOutput = false;
    /**
     * Das zu verwendende Template
     *
     * @var string
     */
    private $template = '';

    /**
     * WidgetController constructor.
     *
     * @param \ACP3\Core\Controller\Context\WidgetContext $context
     */
    public function __construct(Core\Controller\Context\WidgetContext $context)
    {
        $this->container = $context->getContainer();
        $this->eventDispatcher = $context->getEventDispatcher();
        $this->acl = $context->getACL();
        $this->user = $context->getUser();
        $this->translator = $context->getTranslator();
        $this->request = $context->getRequest();
        $this->router = $context->getRouter();
        $this->validator = $context->getValidator();
        $this->view = $context->getView();
        $this->modules = $context->getModules();
        $this->config = $context->getConfig();
        $this->appPath = $context->getAppPath();
        $this->response = $context->getResponse();
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
    protected function applyTemplateAutomatically()
    {
        return $this->request->getModule() . '/' . ucfirst($this->request->getArea()) . '/' . $this->request->getController() . '.' . $this->request->getAction() . '.tpl';
    }

    protected function addCustomTemplateVarsBeforeOutput()
    {
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    protected function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Core\User
     */
    protected function getUser()
    {
        return $this->user;
    }

    /**
     * @return \ACP3\Core\View
     */
    protected function getView()
    {
        return $this->view;
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

    /**
     * @return string
     */
    protected function getApplicationMode()
    {
        return $this->container->getParameter('core.environment');
    }
}
