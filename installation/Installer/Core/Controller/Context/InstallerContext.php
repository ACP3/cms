<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Installer\Core\Controller\Context;

use ACP3\Core\Controller\ActionResultFactory;
use ACP3\Core\Http\RequestInterface;
use ACP3\Core\I18n\LocaleInterface;
use ACP3\Core\I18n\TranslatorInterface;
use ACP3\Core\Router\RouterInterface;
use ACP3\Installer\Core\Environment\ApplicationPath;
use Psr\Container\ContainerInterface;
use Symfony\Component\HttpFoundation\Response;

class InstallerContext
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    private $response;
    /**
     * @var \ACP3\Core\Router\RouterInterface
     */
    private $router;
    /**
     * @var \ACP3\Core\View
     */
    private $view;
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    private $appPath;
    /**
     * @var ActionResultFactory
     */
    private $actionResultFactory;
    /**
     * @var LocaleInterface
     */
    private $locale;

    /**
     * InstallerContext constructor.
     * @param ContainerInterface $container
     * @param TranslatorInterface $translator
     * @param LocaleInterface $locale
     * @param RequestInterface $request
     * @param RouterInterface $router
     * @param \ACP3\Core\View $view
     * @param Response $response
     * @param ApplicationPath $appPath
     * @param ActionResultFactory $actionResultFactory
     */
    public function __construct(
        ContainerInterface $container,
        TranslatorInterface $translator,
        LocaleInterface $locale,
        RequestInterface $request,
        RouterInterface $router,
        \ACP3\Core\View $view,
        Response $response,
        ApplicationPath $appPath,
        ActionResultFactory $actionResultFactory
    ) {
        $this->container = $container;
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->response = $response;
        $this->appPath = $appPath;
        $this->actionResultFactory = $actionResultFactory;
        $this->locale = $locale;
    }

    /**
     * @return ContainerInterface
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @return TranslatorInterface
     */
    public function getTranslator()
    {
        return $this->translator;
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
     * @return \ACP3\Core\View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return \ACP3\Installer\Core\Environment\ApplicationPath
     */
    public function getAppPath()
    {
        return $this->appPath;
    }

    /**
     * @return ActionResultFactory
     */
    public function getActionResultFactory(): ActionResultFactory
    {
        return $this->actionResultFactory;
    }

    /**
     * @return LocaleInterface
     */
    public function getLocale(): LocaleInterface
    {
        return $this->locale;
    }
}
