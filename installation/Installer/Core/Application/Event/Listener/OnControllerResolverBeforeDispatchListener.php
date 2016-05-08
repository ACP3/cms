<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Application\Event\Listener;


use ACP3\Core\Http\RequestInterface;
use ACP3\Core\Redirect;

/**
 * Class OnControllerResolverBeforeDispatchListener
 * @package ACP3\Installer\Core\Application\Event\Listener
 */
class OnControllerResolverBeforeDispatchListener
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Redirect
     */
    private $redirect;

    /**
     * OnFrontControllerBeforeDispatchListener constructor.
     * @param RequestInterface $request
     * @param Redirect $redirect
     */
    public function __construct(
        RequestInterface $request,
        Redirect $redirect)
    {
        $this->request = $request;
        $this->redirect = $redirect;
    }

    /**
     * If the language has been changed, set a cookie with the new default language and force a page reload
     */
    public function onLanguageChange()
    {
        if ($this->request->getPost()->has('lang')) {
            setcookie('ACP3_INSTALLER_LANG', $this->request->getPost()->get('lang', ''), time() + 3600, '/');
            $this->redirect->temporary($this->request->getFullPath())->send();
            exit;
        }
    }
}
