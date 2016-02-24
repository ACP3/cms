<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers. See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Controller\Context;

use ACP3\Core\Http\RequestInterface;
use ACP3\Installer\Core\Environment\ApplicationPath;
use ACP3\Installer\Core\I18n\Translator;
use ACP3\Installer\Core\Router;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class InstallerContext
 * @package ACP3\Installer\Core\Controller\Context
 */
class InstallerContext
{
    /**
     * @var \ACP3\Installer\Core\I18n\Translator
     */
    protected $translator;
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    protected $request;
    /**
     * @var \Symfony\Component\HttpFoundation\Response
     */
    protected $response;
    /**
     * @var \ACP3\Installer\Core\Router
     */
    protected $router;
    /**
     * @var \ACP3\Core\View
     */
    protected $view;
    /**
     * @var \ACP3\Installer\Core\Environment\ApplicationPath
     */
    protected $appPath;

    /**
     * Context constructor.
     *
     * @param \ACP3\Installer\Core\I18n\Translator             $translator
     * @param \ACP3\Core\Http\RequestInterface                 $request
     * @param \ACP3\Installer\Core\Router                      $router
     * @param \ACP3\Core\View                                  $view
     * @param \Symfony\Component\HttpFoundation\Response       $response
     * @param \ACP3\Installer\Core\Environment\ApplicationPath $appPath
     */
    public function __construct(
        Translator $translator,
        RequestInterface $request,
        Router $router,
        \ACP3\Core\View $view,
        Response $response,
        ApplicationPath $appPath
    )
    {
        $this->translator = $translator;
        $this->request = $request;
        $this->router = $router;
        $this->view = $view;
        $this->response = $response;
        $this->appPath = $appPath;
    }

    /**
     * @return \ACP3\Installer\Core\I18n\Translator
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
     * @return \ACP3\Installer\Core\Router
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
}
