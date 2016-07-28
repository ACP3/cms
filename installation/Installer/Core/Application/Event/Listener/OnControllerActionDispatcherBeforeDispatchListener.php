<?php
/**
 * Copyright (c) 2016 by the ACP3 Developers.
 * See the LICENCE file at the top-level module directory for licencing details.
 */

namespace ACP3\Installer\Core\Application\Event\Listener;


use ACP3\Core\Http\RedirectResponse;
use ACP3\Core\Http\RequestInterface;

/**
 * Class OnControllerActionDispatcherBeforeDispatchListener
 * @package ACP3\Installer\Core\Application\Event\Listener
 */
class OnControllerActionDispatcherBeforeDispatchListener
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var RedirectResponse
     */
    private $redirect;

    /**
     * OnFrontControllerBeforeDispatchListener constructor.
     * @param RequestInterface $request
     * @param RedirectResponse $redirect
     */
    public function __construct(
        RequestInterface $request,
        RedirectResponse $redirect)
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
