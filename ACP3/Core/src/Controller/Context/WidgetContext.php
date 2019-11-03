<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Controller\Context;

use ACP3\Core\ACL;
use ACP3\Core\Authentication\Model\UserModelInterface;
use ACP3\Core\Environment\ApplicationPath;
use ACP3\Core\Helpers\ResultsPerPage;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\Translator;
use ACP3\Core\Modules;
use ACP3\Core\Router\RouterInterface;
use ACP3\Core\Settings\SettingsInterface;
use ACP3\Core\Validation\Validator;
use ACP3\Core\View;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;

class WidgetContext
{
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
     * @var \ACP3\Core\Authentication\Model\UserModelInterface
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
     * @var \ACP3\Core\Router\RouterInterface
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
     * @var SettingsInterface
     */
    protected $config;
    /**
     * @var \ACP3\Core\Environment\ApplicationPath
     */
    protected $appPath;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;
    /**
     * @var ResultsPerPage
     */
    private $resultsPerPage;

    /**
     * WidgetContext constructor.
     */
    public function __construct(
        ContainerInterface $container,
        EventDispatcherInterface $eventDispatcher,
        ACL $acl,
        UserModelInterface $user,
        Translator $translator,
        Modules $modules,
        RequestInterface $request,
        RouterInterface $router,
        Validator $validator,
        View $view,
        SettingsInterface $config,
        ApplicationPath $appPath,
        Response $response,
        ResultsPerPage $resultsPerPage
    ) {
        $this->container = $container;
        $this->eventDispatcher = $eventDispatcher;
        $this->acl = $acl;
        $this->user = $user;
        $this->translator = $translator;
        $this->modules = $modules;
        $this->request = $request;
        $this->router = $router;
        $this->validator = $validator;
        $this->view = $view;
        $this->config = $config;
        $this->appPath = $appPath;
        $this->response = $response;
        $this->resultsPerPage = $resultsPerPage;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    public function getEventDispatcher()
    {
        return $this->eventDispatcher;
    }

    /**
     * @return \ACP3\Core\ACL
     */
    public function getACL()
    {
        return $this->acl;
    }

    /**
     * @return \ACP3\Core\Authentication\Model\UserModelInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return \ACP3\Core\I18n\Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return \ACP3\Core\Modules
     */
    public function getModules()
    {
        return $this->modules;
    }

    /**
     * @return \ACP3\Core\Http\RequestInterface
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * @return \ACP3\Core\Router\RouterInterface
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     * @return \ACP3\Core\Validation\Validator
     */
    public function getValidator()
    {
        return $this->validator;
    }

    /**
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return \ACP3\Core\Settings\SettingsInterface
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * @return \ACP3\Core\Environment\ApplicationPath
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return ResultsPerPage
     */
    public function getResultsPerPage()
    {
        return $this->resultsPerPage;
    }
}