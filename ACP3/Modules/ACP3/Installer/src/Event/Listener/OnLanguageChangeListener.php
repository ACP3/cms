<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Modules\ACP3\Installer\Event\Listener;

use ACP3\Core\Http\RedirectResponse;
use ACP3\Core\Http\RequestInterface;

class OnLanguageChangeListener
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
     */
    public function __construct(
        RequestInterface $request,
        RedirectResponse $redirect
    ) {
        $this->request = $request;
        $this->redirect = $redirect;
    }

    /**
     * If the language has been changed, set a cookie with the new default language and force a page reload.
     */
    public function __invoke()
    {
        if ($this->request->getPost()->has('lang')) {
            \setcookie('ACP3_INSTALLER_LANG', $this->request->getPost()->get('lang', ''), \time() + 3600, '/');
            $this->redirect->temporary($this->request->getFullPath())->send();
            exit;
        }
    }
}
