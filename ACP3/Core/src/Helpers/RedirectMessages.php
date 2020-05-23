<?php

/**
 * Copyright (c) by the ACP3 Developers.
 * See the LICENSE file at the top-level module directory for licensing details.
 */

namespace ACP3\Core\Helpers;

use ACP3\Core;
use Symfony\Component\HttpFoundation\Session\Session;

class RedirectMessages
{
    /**
     * @var \ACP3\Core\Http\RequestInterface
     */
    private $request;
    /**
     * @var \ACP3\Core\Http\RedirectResponse
     */
    private $redirect;
    /**
     * @var \Symfony\Component\HttpFoundation\Session\Session
     */
    private $sessionHandler;

    public function __construct(
        Core\Http\RedirectResponse $redirect,
        Core\Http\RequestInterface $request,
        Session $sessionHandler
    ) {
        $this->redirect = $redirect;
        $this->request = $request;
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * Gets the generated redirect message from setMessage().
     *
     * @return array
     *
     * @throws \Exception
     */
    public function getMessage(): array
    {
        return $this->sessionHandler->getFlashBag()->all();
    }

    /**
     * Sets a redirect messages and redirects to the given internal path.
     *
     * @param int|bool    $success
     * @param string      $text
     * @param string|null $path
     *
     * @return \Symfony\Component\HttpFoundation\JsonResponse|\Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function setMessage($success, $text, $path = null)
    {
        $this->sessionHandler->getFlashBag()
            ->set((bool) $success ? 'success' : 'error', $text);

        // If no path has been given, guess it automatically
        if ($path === null) {
            $path = $this->request->getModuleAndController();
        }

        return $this->redirect->temporary($path);
    }
}
